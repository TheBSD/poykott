<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Support\Str;

class MatrixAlternativesController extends Controller
{
    /**
     * Each csv must have these, subheadings can vary
     *
     * @var array
     */
    private const COMMON_HEADINGS = [
        'Features',
        'Security and Compliance',
        'Pricing',
        'Israel Presence & Ties Assessment',
        'Recommendation & Risk Summary',
    ];

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
        $file = $this->getCsvFile($company);
        abort_unless($file !== null, 404, 'Matrix CSV not found for ' . $company);

        ['rows' => $rows, 'sections' => $sections, 'weights' => $weights] = $this->parseMatrixCsv($file);
        $rows = $this->attachCompanyImages($rows);
        $selected = $this->findRowByName($rows, $company);
        $searchedCompany = $selected;

        $companyModel = Company::query()
            ->where('slug', Str::slug($company))
            ->orWhere('name', $company)
            ->first();

        $columnWeights = [
            'features' => $weights['Features'] ?? 0,
            'security' => $weights['Security and Compliance'] ?? 0,
            'pricing' => $weights['Pricing'] ?? 0,
            'islPresence' => $weights['Israel Presence & Ties Assessment'] ?? 0,
        ];

        return view('matrix.show', [
            'company' => $company,
            'logoPath' => $companyModel?->image_path ?? asset('images/logos/' . Str::slug($company) . '.svg'),
            'rows' => $this->enrichRowsForDisplay($this->sortByScore($rows, $company), $company),
            'selected' => $selected,
            'searchedCompany' => $searchedCompany,
            'bestScore' => $this->calculateBestScore($rows, $company),
            'comparisonData' => $this->prepareComparisonData($selected, $searchedCompany),
            'orderedSections' => $this->buildOrderedSections($sections),
            'columnWeights' => $columnWeights,
            'renderCellValue' => fn (?array $row, string $key): string => $this->renderCellValue($row, $key),
        ]);
    }

    public function details(string $alternative, string $company)
    {
        $file = $this->getCsvFile($company);
        abort_unless($file !== null, 404, 'Matrix CSV not found for ' . $company);

        ['rows' => $rows, 'sections' => $sections] = $this->parseMatrixCsv($file);
        $rows = $this->attachCompanyImages($rows);
        $selected = $this->findRowByName($rows, $alternative);
        $searchedCompany = $this->findRowByName($rows, $company);

        $companyModel = Company::query()
            ->where('slug', Str::slug($company))
            ->orWhere('name', $company)
            ->first();

        return view('matrix.details', [
            'company' => $company,
            'logoPath' => $companyModel?->image_path ?? asset('images/logos/' . Str::slug($company) . '.svg'),
            'rows' => $rows,
            'selected' => $selected,
            'searchedCompany' => $searchedCompany,
            'comparisonData' => $this->prepareComparisonData($selected, $searchedCompany),
            'orderedSections' => $this->buildOrderedSections($sections),
            'renderCellValue' => fn (?array $row, string $key): string => $this->renderCellValue($row, $key),
            'getCellScore' => fn (?array $row, string $key): string => $this->getCellScore($row, $key),
        ]);
    }

    private function parseMatrixCsv(string $file): array
    {
        $csvData = array_map('str_getcsv', file($file));

        $commonHeadingIndices = [];
        $subheadings = [''];
        $weights = [];
        $scores = [];
        $descriptions = [];
        $companies = [];
        $sections = [];
        $currentHeading = null;
        $counter = count($csvData);

        for ($i = 0; $i < $counter; $i++) {
            for ($j = 0; $j < count($csvData[$i]); $j++) {
                $cell = $csvData[$i][$j] ?? '';

                if ($j === 0 && in_array($cell, self::COMMON_HEADINGS)) {
                    $commonHeadingIndices[$cell] = ['row' => $i, 'col' => $j];
                    $currentHeading = $cell;
                    $sections[$currentHeading] = ['subheadings' => []];
                } elseif ($j === 0 && $cell !== '' && ! in_array($cell, self::COMMON_HEADINGS)) {
                    $subheadings[] = $cell;
                    if ($currentHeading !== null) {
                        $sections[$currentHeading]['subheadings'][] = $cell;
                    }
                }

                if ($j === 1 && is_numeric($cell)) {
                    $rowLabel = $csvData[$i][0] ?? '';
                    if (isset($commonHeadingIndices[$rowLabel]) || in_array($rowLabel, $subheadings)) {
                        $weights[$rowLabel] = floatval($cell);
                    }
                }

                if ($i === 0 && $j >= 2 && $cell !== '') {
                    $companies[] = $cell;
                } elseif ($j >= 2 && ($csvData[$i][0] ?? '') !== '' && $cell !== '' && ($csvData[0][$j] ?? '') !== '') {
                    // Score column: row 0 has a company name here
                    $scores[$csvData[0][$j]][$csvData[$i][0]] = floatval($cell);
                } elseif ($j >= 2 && ($csvData[$i][0] ?? '') !== '' && $cell !== '' && ($csvData[0][$j] ?? '') === '' && isset($csvData[0][$j - 1]) && $csvData[0][$j - 1] !== '') {
                    // Description column: row 0 is empty, previous column has the company name
                    $descriptions[$csvData[0][$j - 1]][$csvData[$i][0]] = $cell;
                }
            }
        }

        $headingKeyMap = [
            'Features' => 'features',
            'Security and Compliance' => 'security',
            'Pricing' => 'pricing',
            'Israel Presence & Ties Assessment' => 'islPresence',
        ];

        $rows = [];
        foreach ($companies as $companyName) {
            $companyScores = $scores[$companyName] ?? [];

            $companyTotal = 0;
            foreach (self::COMMON_HEADINGS as $heading) {
                $companyTotal += $companyScores[$heading] ?? 0;
            }

            $row = [
                'name' => $companyName,
                'totalScore' => $companyTotal,
            ];

            foreach ($companyScores as $heading => $score) {
                $row[$heading] = $score;
            }

            foreach ($descriptions[$companyName] ?? [] as $heading => $description) {
                $row[$heading . '_description'] = $description;
            }

            foreach ($headingKeyMap as $fullName => $shortKey) {
                if (isset($companyScores[$fullName])) {
                    $row[$shortKey] = $companyScores[$fullName];
                }
            }

            $rows[] = $row;
        }

        return ['rows' => $rows, 'sections' => $sections, 'weights' => $weights];
    }

    private function findRowByName(array $rows, string $name): ?array
    {
        $slug = Str::slug($name);
        foreach ($rows as $row) {
            if (Str::slug($row['name'] ?? '') === $slug) {
                return $row;
            }
        }

        return null;
    }

    private function buildOrderedSections(array $sections): array
    {
        $ordered = [];
        foreach (self::COMMON_HEADINGS as $heading) {
            if (isset($sections[$heading])) {
                $ordered[] = [
                    'title' => $heading,
                    'items' => $sections[$heading]['subheadings'],
                ];
            }
        }

        return $ordered;
    }

    private function renderCellValue(?array $row, string $key): string
    {
        if ($row === null) {
            return '—';
        }

        $value = $row[$key . '_description'] ?? $row[$key] ?? null;

        return $value !== null ? (string) $value : '—';
    }

    private function getCellScore(?array $row, string $key): string
    {
        if ($row === null) {
            return '—';
        }

        $score = $row[$key] ?? null;

        return $score !== null ? (string) $score : '—';
    }

    private function attachCompanyImages(array $rows): array
    {
        foreach ($rows as &$row) {
            $name = $row['name'] ?? '';
            $slug = Str::slug($name);
            $model = Company::query()
                ->where('slug', $slug)
                ->orWhere('name', $name)
                ->first();

            $row['image_path'] = $model?->image_path ?? asset('images/logos/' . $slug . '.svg');
        }
        unset($row);

        return $rows;
    }

    private function getCsvFile(string $company): ?string
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
                'logoPath' => $cleanRow['image_path'] ?? '',
            ]);
        }, $rows);
    }

    private function sortByScore(array $rows, string $pinnedCompany): array
    {
        $pinned = Str::slug($pinnedCompany);

        usort($rows, function (array $a, array $b) use ($pinned): int {
            $aIsPinned = Str::slug($a['name'] ?? '') === $pinned;
            $bIsPinned = Str::slug($b['name'] ?? '') === $pinned;

            if ($aIsPinned) {
                return -1;
            }

            if ($bIsPinned) {
                return 1;
            }

            return ($b['totalScore'] ?? 0) <=> ($a['totalScore'] ?? 0);
        });

        return $rows;
    }

    private function prepareComparisonData(?array $selected, ?array $searched): array
    {
        return [
            'selectedLogo' => $selected['image_path'] ?? null,
            'searchedLogo' => $searched['image_path'] ?? null,
            'selectedPercent' => ($selected !== null && $selected !== []) ? round($selected['totalScore'] ?? 0) : null,
            'searchedPercent' => ($searched !== null && $searched !== []) ? round($searched['totalScore'] ?? 0) : null,
        ];
    }
}
