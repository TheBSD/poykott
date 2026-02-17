<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Support\Str;

class MatrixAlternativesController extends Controller
{
    // ─── Column maximums exposed to views ────────────────────────────────────

    private const COLUMN_MAXES = [
        'features' => 25,
        'security' => 5,
        'pricing' => 30,
        'islPresence' => 40,
    ];

    // ─── Public actions ───────────────────────────────────────────────────────

    public function index()
    {
        if ($company = request()->query('company')) {
            return redirect()->route('matrix.show', ['company' => $company]);
        }

        $dir = storage_path('app/matrix');
        $files = [];

        if (is_dir($dir)) {
            foreach (glob($dir . '/*.csv') as $path) {
                $name = pathinfo($path, PATHINFO_FILENAME);
                $slug = Str::slug($name);
                $model = Company::query()
                    ->where('slug', $slug)
                    ->orWhere('name', $name)
                    ->first();

                $files[] = [
                    'slug' => $slug,
                    'name' => $name,
                    'image_path' => $model?->image_path ?? asset('images/logos/' . $slug . '.svg'),
                ];
            }
        }

        return view('matrix.index', ['companies' => $files]);
    }

    public function show(string $company)
    {
        $file = $this->resolveCsvFile($company);
        abort_unless($file !== null, 404, 'Matrix CSV not found for ' . $company);

        ['rows' => $rows, 'sections' => $sections] = $this->parseMatrixCsv($file);
        $selected = $this->findRowByName($rows, $company);
        $searchedCompany = $selected;

        return view('matrix.show', [
            'company' => $company,
            'rows' => $this->enrichRowsForDisplay($rows, $company),
            'selected' => $selected,
            'searchedCompany' => $searchedCompany,
            'bestScore' => $this->calculateBestScore($rows, $company),
            'comparisonData' => $this->prepareComparisonData($selected, $searchedCompany),
            'orderedSections' => $this->buildOrderedSections($sections),
            'columnMaxes' => self::COLUMN_MAXES,
            'renderCellValue' => fn (?array $row, string $key): string => $this->renderCellValue($row, $key),
        ]);
    }

    public function details(string $alternative, string $company)
    {
        $file = $this->resolveCsvFile($company);
        abort_unless($file !== null, 404, 'Matrix CSV not found for ' . $company);

        ['rows' => $rows, 'sections' => $sections] = $this->parseMatrixCsv($file);
        $selected = $this->findRowByName($rows, $alternative);
        $searchedCompany = $this->findRowByName($rows, $company);

        return view('matrix.details', [
            'company' => $company,
            'rows' => $rows,
            'selected' => $selected,
            'searchedCompany' => $searchedCompany,
            'comparisonData' => $this->prepareComparisonData($selected, $searchedCompany),
            'orderedSections' => $this->buildOrderedSections($sections),
            'renderCellValue' => fn (?array $row, string $key): string => $this->renderCellValue($row, $key),
            'getCellScore' => fn (?array $row, string $key): string => $this->getCellScore($row, $key),
        ]);
    }

    // ─── CSV resolution ───────────────────────────────────────────────────────

    private function resolveCsvFile(string $company): ?string
    {
        $dir = storage_path('app/matrix');
        $direct = $dir . '/' . $company . '.csv';

        if (file_exists($direct)) {
            return $direct;
        }

        $slug = Str::slug($company);
        foreach (glob($dir . '/*.csv') as $path) {
            $name = pathinfo($path, PATHINFO_FILENAME);
            if (
                Str::slug($name) === $slug
                || Str::contains(Str::slug($name), $slug)
                || Str::contains(Str::lower($name), Str::lower($company))
            ) {
                return $path;
            }
        }

        return null;
    }

    // ─── Row lookup ───────────────────────────────────────────────────────────

    private function findRowByName(array $rows, string $name): ?array
    {
        $target = Str::slug($name);
        foreach ($rows as $row) {
            if (isset($row['name']) && Str::slug($row['name']) === $target) {
                return $row;
            }
        }

        return null;
    }

    // ─── CSV parsing ──────────────────────────────────────────────────────────

    private function parseMatrixCsv(string $file): array
    {
        $raw = $this->readRawCsv($file);

        if ($raw === []) {
            return ['rows' => [], 'sections' => []];
        }

        return $this->isTransposedFormat($raw[0])
            ? $this->parseTransposedCsv($raw)
            : $this->parseSimpleCsv($raw);
    }

    private function readRawCsv(string $file): array
    {
        abort_if(($handle = fopen($file, 'r')) === false, 500, 'Failed to open CSV: ' . basename($file));

        $raw = [];
        while (($row = fgetcsv($handle)) !== false) {
            $raw[] = $row;
        }
        fclose($handle);

        return $raw;
    }

    private function isTransposedFormat(array $firstRow): bool
    {
        $joined = implode(' ', $firstRow);

        return stripos($joined, 'name') === false
            && stripos($joined, 'weight') !== false
            && count($firstRow) > 2;
    }

    // ─── Transposed CSV parsing ───────────────────────────────────────────────

    private function parseTransposedCsv(array $raw): array
    {
        $vendorCols = $this->extractVendorColumns($raw[0]);

        [$vendorMetrics, $metricWeights, $hierarchy] = $this->extractVendorMetrics($raw, $vendorCols);

        $rows = $this->buildVendorRows($vendorCols, $vendorMetrics);
        $mappedRows = array_map([$this, 'normalizeRowKeys'], $rows);
        $weightLookup = $this->buildMetricWeightLookup($metricWeights);

        return [
            'rows' => $this->computeScoresForRows($mappedRows, $weightLookup),
            'sections' => $this->normalizeHierarchy($hierarchy),
        ];
    }

    /**
     * Return [col_index => vendor_name] for every even score column (cols >= 2).
     * Even-indexed columns (2, 4, 6...) are score columns;
     * their odd-indexed neighbours (3, 5, 7...) are description siblings.
     */
    private function extractVendorColumns(array $firstRow): array
    {
        $vendorCols = [];
        foreach ($firstRow as $c => $val) {
            if ($c <= 1) {
                continue;
            }
            $val = trim((string) $val);
            if ($val === '') {
                continue;
            }
            if (preg_match('/weight/i', $val)) {
                continue;
            }
            if ($c % 2 === 0) {
                $vendorCols[$c] = $val;
            }
        }

        return $vendorCols;
    }

    /**
     * Walk data rows of transposed CSV and return:
     *   [0] vendorMetrics   - [vendorName => [metric => value, metric_description => text]]
     *   [1] metricWeights   - [metricName => float]
     *   [2] metricHierarchy - [parentMetric => [childMetric, ...]]
     */
    private function extractVendorMetrics(array $raw, array $vendorCols): array
    {
        $vendorMetrics = [];
        $metricWeights = [];
        $metricHierarchy = [];
        $currentParent = null;

        for ($r = 1, $total = count($raw); $r < $total; $r++) {
            $row = $raw[$r];
            $metric = isset($row[0]) ? trim((string) $row[0]) : null;

            if ($metric === null || $metric === '') {
                $currentParent = null;

                continue;
            }

            $weight = $this->parseWeightCell($row[1] ?? null);
            if ($weight !== null) {
                $metricWeights[$metric] = $weight;
                $currentParent = $metric;
                $metricHierarchy[$currentParent] ??= [];
            } elseif ($currentParent !== null) {
                $metricHierarchy[$currentParent][] = $metric;
            }

            foreach ($vendorCols as $c => $name) {
                $vendorMetrics[$name][$metric] = $row[$c] ?? null;

                $desc = $row[$c + 1] ?? null;
                if (! empty($desc)) {
                    $vendorMetrics[$name][$metric . '_description'] = $desc;
                }
            }
        }

        return [$vendorMetrics, $metricWeights, $metricHierarchy];
    }

    private function parseWeightCell(mixed $raw): ?float
    {
        if ($raw === null) {
            return null;
        }
        $s = rtrim(trim((string) $raw), "%\t \n\r");
        if (is_numeric($s)) {
            return (float) $s;
        }
        if (preg_match('/(\d+)/', (string) $raw, $m)) {
            return (float) $m[1];
        }

        return null;
    }

    private function buildVendorRows(array $vendorCols, array $vendorMetrics): array
    {
        $rows = [];
        foreach ($vendorCols as $name) {
            $entry = ['name' => $name];
            foreach ($vendorMetrics[$name] ?? [] as $metric => $val) {
                $entry[$metric] = $val;
            }
            $rows[] = $entry;
        }

        return $rows;
    }

    // ─── Simple (header-row) CSV parsing ─────────────────────────────────────

    private function parseSimpleCsv(array $raw): array
    {
        $headers = $raw[0];
        $rows = [];

        for ($i = 1, $total = count($raw); $i < $total; $i++) {
            if (count($raw[$i]) === count($headers)) {
                $rows[] = array_combine($headers, $raw[$i]);
            }
        }

        // Simple CSV has no hierarchy metadata; sections fall back to defaults.
        return [
            'rows' => $this->computeScoresForRows($rows, []),
            'sections' => [],
        ];
    }

    // ─── Scoring ──────────────────────────────────────────────────────────────

    private function computeScoresForRows(array $rows, array $weightLookup): array
    {
        $metricKeyMap = $this->getMetricKeyMap();
        $defaultWeights = $this->getDefaultWeights();

        foreach ($rows as $i => $row) {
            $rows[$i]['features'] = 0;
            $rows[$i]['security'] = 0;
            $rows[$i]['pricing'] = 0;
            $rows[$i]['islPresence'] = 0;

            foreach ($metricKeyMap as $metricName => $info) {
                $weight = $weightLookup[$metricName] ?? ($defaultWeights[$metricName] ?? 0);
                if ($weight <= 0) {
                    continue;
                }

                $nVal = $this->toNumber($this->findValueInRow($row, $metricName));

                if ($nVal !== null) {
                    $rows[$i][$info['key']] = (int) round(min($info['weight'], max(0, $nVal)));
                }
            }

            $rows[$i]['totalScore'] = $rows[$i]['features']
            + $rows[$i]['security']
            + $rows[$i]['pricing']
            + $rows[$i]['islPresence'];
        }

        return $rows;
    }

    private function getMetricKeyMap(): array
    {
        return [
            'Features' => ['key' => 'features',    'weight' => 25],
            'Security and Compliance' => ['key' => 'security',    'weight' => 5],
            'Pricing' => ['key' => 'pricing',     'weight' => 30],
            'ISL Presence & Ties Assessment' => ['key' => 'islPresence', 'weight' => 40],
        ];
    }

    private function getDefaultWeights(): array
    {
        return [
            'Features' => 25,
            'Security and Compliance' => 5,
            'Pricing' => 30,
            'ISL Presence & Ties Assessment' => 40,
        ];
    }

    private function toNumber(mixed $v): ?float
    {
        if ($v === null) {
            return null;
        }
        $s = rtrim(trim((string) $v), "%\t \n\r");
        if ($s === '') {
            return null;
        }
        if (is_numeric($s)) {
            return (float) $s;
        }
        if (preg_match('/(-?\d+(?:\.\d+)?)/', $s, $m)) {
            return (float) $m[1];
        }

        return null;
    }

    private function findValueInRow(array $row, string $metricName): mixed
    {
        if (isset($row[$metricName])) {
            return $row[$metricName];
        }
        foreach ($row as $k => $v) {
            if (strtolower($k) === strtolower($metricName)) {
                return $v;
            }
        }

        return null;
    }

    // ─── Key normalization ────────────────────────────────────────────────────

    private function getNormalizeMap(): array
    {
        return [
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
    }

    private function normalizeMetricKey(string $key): string
    {
        foreach ($this->getNormalizeMap() as $pattern => $target) {
            if (preg_match($pattern, $key)) {
                return $target;
            }
        }

        return $key;
    }

    /**
     * Re-key all metrics in a single row through the normalize map.
     * _description suffixes are preserved on the normalized base key.
     */
    private function normalizeRowKeys(array $row): array
    {
        $mapped = ['name' => $row['name'] ?? null];

        foreach ($row as $k => $v) {
            if ($k === 'name') {
                continue;
            }

            $isDesc = str_ends_with($k, '_description');
            $baseKey = $isDesc ? substr($k, 0, -strlen('_description')) : $k;
            $normKey = $this->normalizeMetricKey($baseKey);
            $finalKey = $isDesc ? $normKey . '_description' : $normKey;

            if (! array_key_exists($finalKey, $mapped) || $mapped[$finalKey] === null) {
                $mapped[$finalKey] = $v;
            }
        }

        return $mapped;
    }

    private function buildMetricWeightLookup(array $metricWeights): array
    {
        $lookup = [];
        foreach ($metricWeights as $k => $v) {
            $lookup[$this->normalizeMetricKey($k)] = $v;
        }

        return $lookup;
    }

    /**
     * Apply normalizeMetricKey to every parent and child in the raw hierarchy map.
     */
    private function normalizeHierarchy(array $hierarchy): array
    {
        $normalized = [];
        foreach ($hierarchy as $parent => $children) {
            $normParent = $this->normalizeMetricKey($parent);
            $normalized[$normParent] = array_map([$this, 'normalizeMetricKey'], $children);
        }

        return $normalized;
    }

    // ─── Cell value helpers ───────────────────────────────────────────────────

    /**
     * Build a flat [normalizedKey => value] map for a row.
     * When $excludeDescriptions is true, _description keys are skipped.
     */
    private function buildNormalizedRowMap(array $row, bool $excludeDescriptions = false): array
    {
        $map = [];
        foreach ($row as $k => $v) {
            if ($excludeDescriptions && str_ends_with($k, '_description')) {
                continue;
            }
            $map[strtolower(str_replace([' ', '_', '-'], '', $k))] = $v;
        }

        return $map;
    }

    /**
     * Core lookup: find a value in a row by key, with normalized fallback.
     */
    private function lookupCellValue(array $row, string $key, bool $excludeDescriptions = false): ?string
    {
        if (! $excludeDescriptions) {
            $descKey = $key . '_description';
            if (isset($row[$descKey]) && $row[$descKey] !== '') {
                return (string) $row[$descKey];
            }
        }

        if (isset($row[$key]) && (! $excludeDescriptions || ! str_ends_with($key, '_description'))) {
            return (string) $row[$key];
        }

        $normMap = $this->buildNormalizedRowMap($row, $excludeDescriptions);
        $normKey = strtolower(str_replace([' ', '_', '-'], '', $key));

        if (isset($normMap[$normKey])) {
            return (string) $normMap[$normKey];
        }

        foreach ($normMap as $k => $v) {
            if (str_contains($k, $normKey) || str_contains($normKey, $k)) {
                return (string) $v;
            }
        }

        return null;
    }

    private function renderCellValue(?array $row, string $key): string
    {
        if ($row === null || $row === []) {
            return '—';
        }

        return $this->lookupCellValue($row, $key) ?? '—';
    }

    private function getCellScore(?array $row, string $key): string
    {
        if ($row === null || $row === []) {
            return '—';
        }

        return $this->lookupCellValue($row, $key, excludeDescriptions: true) ?? '—';
    }

    // ─── Display helpers ──────────────────────────────────────────────────────

    /**
     * Build display sections whose titles are fixed but whose items are driven
     * by the hierarchy extracted from the CSV.
     *
     * Sub-item rows in the CSV often carry a numeric score in the weight column,
     * which causes the parser to register them as top-level entries in $hierarchy
     * instead of as children. We recover the correct grouping here by walking the
     * hierarchy in insertion order and bucketing every non-scoring-category key
     * under the most recently seen scoring category.
     *
     * @param  array  $hierarchy  Normalised [metric => [child, ...]] from the CSV.
     */
    private function buildOrderedSections(array $hierarchy): array
    {
        $scoringKeys = array_keys($this->getMetricKeyMap());
        $scoringKeySet = array_flip($scoringKeys);

        // '_rec' is a virtual bucket for items that appear before the first scoring category.
        $buckets = array_merge(['_rec' => []], array_fill_keys($scoringKeys, []));
        $currentBucket = '_rec';

        foreach ($hierarchy as $key => $children) {
            if (isset($scoringKeySet[$key])) {
                $currentBucket = $key;
                // Absorb any true weightless children the parser captured.
                foreach ($children as $child) {
                    $buckets[$currentBucket][] = $child;
                }
            } else {
                // Sub-item mis-classified as a top-level parent (it had a numeric
                // score in the weight column) — bucket it under the current section.
                $buckets[$currentBucket][] = $key;
                foreach ($children as $child) {
                    $buckets[$currentBucket][] = $child;
                }
            }
        }

        $recItems = $buckets['_rec'] ?: ['Overall Risk Level', 'Best Use Case', 'Recommendation'];

        // Items captured into _rec may also appear at the tail of another bucket
        // (e.g. the CSV repeats them after the ISL section). Strip duplicates.
        $recSet = array_flip($recItems);
        foreach ($scoringKeys as $k) {
            $buckets[$k] = array_values(array_filter(
                $buckets[$k],
                fn ($item): bool => ! isset($recSet[$item])
            ));
        }

        $sections = [
            ['title' => 'Recommendation and Risk Summary', 'items' => $recItems],
        ];

        $titleMap = [
            'Features' => 'Features',
            'Security and Compliance' => 'Security and Compliance',
            'Pricing' => 'Pricing',
            'ISL Presence & Ties Assessment' => 'ISL Presence & Ties Assessment',
        ];

        foreach ($titleMap as $title => $key) {
            $items = $buckets[$key] ?? [];
            if ($items !== []) {
                $sections[] = ['title' => $title, 'items' => $items];
            }
        }

        return $sections;
    }

    private function calculateBestScore(array $rows, string $company): ?int
    {
        $target = Str::slug($company);
        $bestScore = null;

        foreach ($rows as $row) {
            if (isset($row['name']) && Str::slug($row['name']) === $target) {
                continue;
            }
            $score = isset($row['totalScore']) ? (int) $row['totalScore'] : 0;
            if ($bestScore === null || $score > $bestScore) {
                $bestScore = $score;
            }
        }

        return $bestScore;
    }

    private function enrichRowsForDisplay(array $rows, string $company): array
    {
        $bestScore = $this->calculateBestScore($rows, $company);
        $target = Str::slug($company);

        return array_map(function (array $row) use ($bestScore, $target): array {
            $score = isset($row['totalScore']) ? (int) $row['totalScore'] : 0;
            $name = $row['name'] ?? '';

            $cleanRow = array_filter(
                $row,
                fn ($k): bool => ! str_ends_with($k, '_description'),
                ARRAY_FILTER_USE_KEY
            );

            return array_merge($cleanRow, [
                'score' => $score,
                'isBest' => $score === $bestScore,
                'isSearched' => Str::slug($name) === $target,
                'logoPath' => isset($cleanRow['logo']) ? asset('images/logos/' . $cleanRow['logo']) : '',
            ]);
        }, $rows);
    }

    private function prepareComparisonData(?array $selected, ?array $searched): array
    {
        return [
            'selectedLogo' => $selected && isset($selected['logo']) ? asset('images/logos/' . $selected['logo']) : null,
            'searchedLogo' => $searched && isset($searched['logo']) ? asset('images/logos/' . $searched['logo']) : null,
            'selectedPercent' => ($selected !== null && $selected !== []) ? round($selected['totalScore'] ?? 0) : null,
            'searchedPercent' => ($searched !== null && $searched !== []) ? round($searched['totalScore'] ?? 0) : null,
        ];
    }
}
