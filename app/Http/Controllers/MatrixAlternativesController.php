<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Support\Str;

class MatrixAlternativesController extends Controller
{
    // List available company CSVs in storage/app/matrix
    public function index()
    {
        // If user submitted a company via query string, redirect to the show route.
        $company = request()->query('company');
        if (! empty($company)) {
            return redirect()->route('matrix.show', ['company' => $company]);
        }

        $dir = storage_path('app/matrix');
        $files = [];

        if (is_dir($dir)) {
            foreach (glob($dir . '/*.csv') as $path) {
                $name = pathinfo($path, PATHINFO_FILENAME);
                $slug = Str::slug($name);

                // Try to find a Company model so we can use the media library image_path.
                $companyModel = Company::query()->where('slug', $slug)
                    ->orWhere('name', $name)
                    ->first();

                $imagePath = $companyModel?->image_path ?? asset('images/logos/' . $slug . '.svg');

                $files[] = [
                    'slug' => $slug,
                    'name' => $name,
                    'image_path' => $imagePath,
                ];
            }
        }

        return view('matrix.index', ['companies' => $files]);
    }

    // Show matrix for a company and optional alternative detail
    public function show(string $company, $alternative = null)
    {
        $slug = Str::slug($company);
        $dir = storage_path('app/matrix');
        $file = $dir . '/' . $company . '.csv';

        // fallback: try slug -> original filename discovery
        if (! file_exists($file)) {
            foreach (glob($dir . '/*.csv') as $path) {
                $name = pathinfo($path, PATHINFO_FILENAME);
                // Match exact slug, slug contains, or filename contains company name (case-insensitive)
                if (Str::slug($name) === $slug
                    || Str::contains(Str::slug($name), $slug)
                    || Str::contains(Str::lower($name), Str::lower($company))) {
                    $file = $path;
                    break;
                }
            }
        }

        abort_unless(file_exists($file), 404, 'Matrix CSV not found for ' . $company);

        $rows = $this->parseMatrixCsv($file);

        $selected = null;
        foreach ($rows as $r) {
            if (isset($r['name']) && Str::slug($r['name']) === Str::slug($alternative)) {
                $selected = $r;
                break;
            }
        }

        // find the searched company row (the company being compared)
        $searchedCompany = null;
        foreach ($rows as $r) {
            if (isset($r['name']) && Str::slug($r['name']) === Str::slug($company)) {
                $searchedCompany = $r;
                break;
            }
        }

        return view('matrix.show', [
            'company' => $company,
            'rows' => $rows,
            'selected' => $selected,
            'searchedCompany' => $searchedCompany,
        ]);
    }

    // Show a dedicated details page for an alternative compared against a company
    public function details(string $alternative, string $company)
    {
        $slug = Str::slug($company);
        $dir = storage_path('app/matrix');
        $file = $dir . '/' . $company . '.csv';

        // fallback: try slug -> original filename discovery
        if (! file_exists($file)) {
            foreach (glob($dir . '/*.csv') as $path) {
                $name = pathinfo($path, PATHINFO_FILENAME);
                if (Str::slug($name) === $slug
                    || Str::contains(Str::slug($name), $slug)
                    || Str::contains(Str::lower($name), Str::lower($company))) {
                    $file = $path;
                    break;
                }
            }
        }

        abort_unless(file_exists($file), 404, 'Matrix CSV not found for ' . $company);

        $rows = $this->parseMatrixCsv($file);

        $selected = null;
        foreach ($rows as $r) {
            if (isset($r['name']) && Str::slug($r['name']) === Str::slug($alternative)) {
                $selected = $r;
                break;
            }
        }

        // find the searched company row (the company being compared)
        $searchedCompany = null;
        foreach ($rows as $r) {
            if (isset($r['name']) && Str::slug($r['name']) === Str::slug($company)) {
                $searchedCompany = $r;
                break;
            }
        }

        return view('matrix.details', [
            'company' => $company,
            'rows' => $rows,
            'selected' => $selected,
            'searchedCompany' => $searchedCompany,
        ]);
    }

    // Parse matrix CSVs produced by either the simple row-per-alternative format
    // or the transposed Excel-export format (criteria rows, vendor columns).
    private function parseMatrixCsv(string $file): array
    {
        $raw = [];
        abort_if(($handle = fopen($file, 'r')) === false, 500, 'Failed to open CSV for parsing: ' . basename($file));
        while (($row = fgetcsv($handle)) !== false) {
            $raw[] = $row;
        }
        fclose($handle);

        if ($raw === []) {
            return [];
        }

        // Heuristic: if the first row does not contain a "name" header and
        // contains a token like "Weight" and multiple non-empty cells, treat
        // it as the transposed (criteria x vendors) format produced by Excel.
        $firstRow = $raw[0];
        $joinedFirst = implode(' ', $firstRow);
        $isTransposed = (stripos($joinedFirst, 'name') === false)
            && (stripos($joinedFirst, 'weight') !== false)
            && count($firstRow) > 2;

        if ($isTransposed) {
            // Collect vendor header columns (skip the first column which is criteria)
            $vendorCols = [];
            foreach ($firstRow as $c => $val) {
                $val = trim((string) $val);
                if ($c === 0) {
                    continue;
                }
                if ($val === '') {
                    continue;
                }
                if (preg_match('/weight/i', $val)) {
                    continue;
                }
                $vendorCols[$c] = $val;
            }

            // For each vendor header column, decide which column in data rows
            // holds the value. Many exports place a weight column immediately
            // before the vendor name, so prefer col-1 when it contains data.
            $valueColForVendor = [];
            foreach ($vendorCols as $c => $name) {
                $valueColForVendor[$c] = isset($raw[1][$c - 1]) && trim((string) ($raw[1][$c - 1])) !== '' ? $c - 1 : $c;
            }

            // Build a map of vendor => metrics, and detect parent metrics with subitems
            $vendorMetrics = [];
            $metricWeights = [];
            $metricHierarchy = []; // Track parent -> [subitems]
            $currentParent = null;
            $counter = count($raw);

            for ($r = 1; $r < $counter; $r++) {
                $row = $raw[$r];
                $metric = isset($row[0]) ? trim((string) $row[0]) : null;
                if ($metric === null || $metric === '') {
                    // Empty row may signal section break, reset parent
                    $currentParent = null;

                    continue;
                }

                // Attempt to read a metric-level weight from column 1 (e.g. "30" or "37%")
                $rawWeight = $row[1] ?? null;
                $hasWeight = false;
                if ($rawWeight !== null) {
                    $w = trim((string) $rawWeight);
                    // strip percent and non-numeric characters
                    $w = rtrim($w, "%\t \n\r");
                    $wNum = is_numeric($w) ? (float) $w : null;
                    if ($wNum === null && preg_match('/(\d+)/', (string) $rawWeight, $m)) {
                        $wNum = (float) $m[1];
                    }
                    if ($wNum !== null) {
                        $metricWeights[$metric] = $wNum;
                        $hasWeight = true;
                    }
                }

                // Structural detection: if current row has a weight and previous row was not parsed as subitems,
                // treat it as a parent metric. Otherwise, treat as subitem of the last parent.
                if ($hasWeight && $currentParent === null) {
                    // This is a parent metric (has weight in column 1)
                    $currentParent = $metric;
                    $metricHierarchy[$currentParent] = [];
                } elseif ($hasWeight && $currentParent !== null) {
                    // New parent metric found
                    $currentParent = $metric;
                    $metricHierarchy[$currentParent] = [];
                } elseif (! $hasWeight && $currentParent !== null) {
                    // This is a subitem of the current parent
                    $metricHierarchy[$currentParent][] = $metric;
                }

                foreach ($vendorCols as $c => $name) {
                    $vc = $valueColForVendor[$c];
                    $value = $row[$vc] ?? null;

                    // If the value is empty but we have an adjacent column, try it
                    // This handles CSVs where column layout varies between rows
                    if ((empty($value) || $value === '') && isset($row[$c])) {
                        $value = $row[$c];
                    } elseif ((empty($value) || $value === '') && $vc + 1 < count($row)) {
                        $value = $row[$vc + 1];
                    }

                    $vendorMetrics[$name][$metric] = $value;
                }
            }

            // Turn vendorMetrics into the expected array-of-assoc-rows format
            $rows = [];
            foreach ($vendorCols as $name) {
                $entry = ['name' => $name];
                if (isset($vendorMetrics[$name])) {
                    foreach ($vendorMetrics[$name] as $metric => $val) {
                        $entry[$metric] = $val;
                    }
                }
                $rows[] = $entry;
            }

            // Normalize metric keys to the canonical headings requested by UX
            $normalizeMap = [
                '/overall risk level/i' => 'Overall Risk Level',
                '/best use case/i' => 'Best Use Case',
                '/recommendation/i' => 'Recommendation',

                '/setup complexity/i' => 'Setup Complexity',
                '/drag and drop editing/i' => 'Drag and Drop editing',
                '/AI services/i' => 'AI Services',
                '/Specialized plugins/i' => 'Specialized Plugins',
                '/All-in-one hosting/i' => 'All-in-one hosting',
                '/Access to code/i' => 'Access to code',
                '/E-Commerce tools/i' => 'E-Commerce tools',

                '/security and compliance/i' => 'Security and Compliance',

                '/israel.*presence|isl.*presence/i' => 'ISL Presence & Ties Assessment',

                '/free tier/i' => 'Free tier',
                '/team tier/i' => 'Team tier',
                '/business tier/i' => 'Business tier',

                '/headquarters/i' => 'Headquarters',
                '/major.*investment/i' => 'Major ISL Investment',
                '/partnership/i' => 'ISL Partnerships',
                '/data center/i' => 'Data Centers',
                '/founder/i' => 'Founder/Leadership',
                '/leadership pro.*isl|leadership pro-isr|leadership pro-israel/i' => 'Leadership Pro ISL Statements',
            ];

            $mappedRows = [];
            $normalizedHierarchy = []; // Track normalized parent -> [normalized subitems]

            foreach ($rows as $r) {
                $mapped = ['name' => $r['name'] ?? null];
                foreach ($r as $k => $v) {
                    if ($k === 'name') {
                        continue;
                    }
                    $normalized = null;
                    foreach ($normalizeMap as $pattern => $target) {
                        if (preg_match($pattern, $k)) {
                            $normalized = $target;
                            break;
                        }
                    }
                    if ($normalized === null) {
                        $normalized = $k;
                    }
                    if (! array_key_exists($normalized, $mapped) || $mapped[$normalized] === null) {
                        $mapped[$normalized] = $v;
                    }
                }
                $mappedRows[] = $mapped;
            }

            // Rebuild normalized hierarchy map
            foreach ($metricHierarchy as $parent => $subitems) {
                $normParent = null;
                foreach ($normalizeMap as $pattern => $target) {
                    if (preg_match($pattern, $parent)) {
                        $normParent = $target;
                        break;
                    }
                }
                $normParent ??= $parent;

                $normSubitems = [];
                foreach ($subitems as $sub) {
                    $normSub = null;
                    foreach ($normalizeMap as $pattern => $target) {
                        if (preg_match($pattern, $sub)) {
                            $normSub = $target;
                            break;
                        }
                    }
                    $normSubitems[] = ($normSub ?? $sub);
                }
                $normalizedHierarchy[$normParent] = $normSubitems;
            }

            // Compute totalScore for each mappedRow using metric weights if available.
            $defaultWeights = [
                'Features' => 25,
                'Security and Compliance' => 5,
                'Pricing' => 30,
                'ISL Presence & Ties Assessment' => 40,
            ];

            // Canonical grouping used for scoring
            $scoringMetrics = ['Features', 'Security and Compliance', 'Pricing', 'ISL Presence & Ties Assessment'];

            // Build metricWeights lookup using normalized keys where possible
            $metricWeightLookup = [];
            foreach ($metricWeights as $k => $v) {
                // normalize metric label to our canonical targets if possible
                $found = null;
                foreach ($normalizeMap as $pattern => $target) {
                    if (preg_match($pattern, $k)) {
                        $found = $target;
                        break;
                    }
                }
                $keyName = $found ?? $k;
                $metricWeightLookup[$keyName] = $v;
            }

            // helper to coerce numeric-like values
            $toNumber = function ($v): ?float {
                if ($v === null) {
                    return null;
                }
                $s = trim((string) $v);
                if ($s === '') {
                    return null;
                }
                // strip percent sign
                $s = rtrim($s, "%\t \n\r");
                // extract first number
                if (is_numeric($s)) {
                    return (float) $s;
                }
                if (preg_match('/(-?\d+(?:\.\d+)?)/', $s, $m)) {
                    return (float) $m[1];
                }

                return null;
            };

            // For each vendor row, compute category scores and totalScore
            foreach ($mappedRows as $i => $mr) {
                $vendorName = $mr['name'] ?? null;
                if ($vendorName === null) {
                    continue;
                }

                // Initialize all category scores
                $mappedRows[$i]['features'] = 0;
                $mappedRows[$i]['security'] = 0;
                $mappedRows[$i]['pricing'] = 0;
                $mappedRows[$i]['islPresence'] = 0;

                // Map canonical metric names to lowercase keys and their weights
                $metricKeyMap = [
                    'Features' => ['key' => 'features', 'weight' => 25],
                    'Security and Compliance' => ['key' => 'security', 'weight' => 5],
                    'Pricing' => ['key' => 'pricing', 'weight' => 30],
                    'ISL Presence & Ties Assessment' => ['key' => 'islPresence', 'weight' => 40],
                ];

                foreach ($scoringMetrics as $metricKey) {
                    $metricWeight = $metricWeightLookup[$metricKey] ?? ($defaultWeights[$metricKey] ?? 0);
                    if ($metricWeight <= 0) {
                        continue;
                    }

                    $metricValue = $mr[$metricKey] ?? null;
                    $mappingInfo = $metricKeyMap[$metricKey] ?? null;

                    // Use ONLY the direct main category value from CSV
                    // Do NOT aggregate from subcategories - they are descriptions/explanations
                    $nVal = $toNumber($metricValue);

                    // Store category score using its weight as maximum
                    if ($mappingInfo && $nVal !== null) {
                        $maxValue = $mappingInfo['weight'];
                        $mappedRows[$i][$mappingInfo['key']] = (int) round(min($maxValue, max(0, $nVal)));
                    }
                }

                // Total score is just the sum of all category scores (max 100)
                $totalScore = $mappedRows[$i]['islPresence'] ?? 0;
                $mappedRows[$i]['totalScore'] = $totalScore;
            }

            return $mappedRows;
        }

        // Fallback: assume first row is headers and subsequent rows are records
        $headers = $raw[0];
        $rows = [];
        $counter = count($raw);
        for ($i = 1; $i < $counter; $i++) {
            $row = $raw[$i];
            if (count($row) === count($headers)) {
                $rows[] = array_combine($headers, $row);
            }
        }

        // Calculate category scores for fallback format
        $metricKeyMap = [
            'Features' => ['key' => 'features', 'weight' => 25],
            'Security and Compliance' => ['key' => 'security', 'weight' => 5],
            'Pricing' => ['key' => 'pricing', 'weight' => 30],
            'ISL Presence & Ties Assessment' => ['key' => 'islPresence', 'weight' => 40],
        ];

        $toNumber = function ($v): ?float {
            if ($v === null) {
                return null;
            }
            $s = trim((string) $v);
            if ($s === '') {
                return null;
            }
            $s = rtrim($s, "%\t \n\r");
            if (is_numeric($s)) {
                return (float) $s;
            }
            if (preg_match('/(-?\d+(?:\.\d+)?)/', $s, $m)) {
                return (float) $m[1];
            }

            return null;
        };

        foreach ($rows as $i => $r) {
            // Initialize all category scores
            $rows[$i]['features'] = 0;
            $rows[$i]['security'] = 0;
            $rows[$i]['pricing'] = 0;
            $rows[$i]['islPresence'] = 0;
            $totalScore = 0;

            foreach ($metricKeyMap as $metricKey => $mappingInfo) {
                // Try exact match first, then case-insensitive matches
                $value = null;
                if (isset($r[$metricKey])) {
                    $value = $r[$metricKey];
                } else {
                    // Try to find matching key case-insensitively
                    foreach ($r as $k => $v) {
                        if (strtolower($k) === strtolower($metricKey)) {
                            $value = $v;
                            break;
                        }
                    }
                }

                $nVal = $toNumber($value);
                if ($nVal !== null) {
                    $maxValue = $mappingInfo['weight'];
                    $rows[$i][$mappingInfo['key']] = (int) round(min($maxValue, max(0, $nVal)));
                    $totalScore += $rows[$i][$mappingInfo['key']];
                }
            }
            $rows[$i]['totalScore'] = $totalScore;
        }

        return $rows;
    }
}
