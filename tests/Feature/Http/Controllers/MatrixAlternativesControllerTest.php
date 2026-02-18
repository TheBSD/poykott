<?php

namespace Tests\Feature\Http\Controllers;

use App\Http\Controllers\MatrixAlternativesController;
use App\Models\Company;
use Illuminate\Support\Str;

use function Pest\Laravel\get;

mutates(MatrixAlternativesController::class);

// ─── Fixture helpers ──────────────────────────────────────────────────────────

/**
 * Transposed CSV (the format the real data uses):
 * - Col 0: criteria label
 * - Col 1: weight
 * - Even cols (2, 4, ...): vendor scores
 * - Odd cols  (3, 5, ...): vendor descriptions
 */
function matrixCsvPath(string $name): string
{
    return storage_path('app/matrix/' . $name . '.csv');
}

function writeTestCsv(string $name): void
{
    $dir = storage_path('app/matrix');
    if (! is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    $rows = [
        // Header row: col0=empty, col1=weight label, even cols=vendor names
        ['', 'Weight/100', $name, '', 'AltCo', ''],

        // Recommendation / Risk section (no weight → children of previous parent,
        // but they appear before any scoring category so they land in _rec bucket)
        ['Overall Risk Level',  5, 'High risk',        'High risk detail',       'Low risk',  'Low risk detail'],
        ['Best Use Case',       5, 'Enterprise',        'Best for large orgs',    'SMB',       'Best for small teams'],
        ['Recommendation',      5, 'Not recommended',   'Avoid this vendor',      'Recommended', 'Good choice'],

        // Features section
        ['Features',           25, 23, '', 17, ''],
        ['Setup Complexity',    9,  9, 'Easy', 5, 'Moderate'],
        ['AI Services',         8,  8, 'Full AI suite', 6, 'Basic AI'],

        // Security section
        ['Security and Compliance', 5, 4, 'SOC2', 3, 'ISO 27001'],

        // Pricing section
        ['Pricing',            30, 15, '', 20, ''],
        ['Free tier',          10, 10, 'Yes', 5, 'Limited'],
        ['Business tier',      20, 15, '$20/mo', 20, '$15/mo'],

        // ISL Presence section
        ['ISL Presence & Ties Assessment', 40, 0, '', 10, ''],
        ['Headquarters',       20,  0, 'HQ in Tel Aviv',    10, 'HQ in Germany'],
        ['ISL Partnerships',   10,  0, 'Yes',               0,  'None'],
        ['Founder/Leadership', 10,  0, 'Ex-IDF founder',    0,  'No ISL ties'],
    ];

    $handle = fopen(matrixCsvPath($name), 'w');
    foreach ($rows as $row) {
        fputcsv($handle, $row);
    }
    fclose($handle);
}

function deleteTestCsv(string $name): void
{
    $path = matrixCsvPath($name);
    if (file_exists($path)) {
        unlink($path);
    }
}

// ─── index ────────────────────────────────────────────────────────────────────

test('index displays view', function (): void {
    writeTestCsv('TestCo');

    $response = get(route('matrix.index'));

    $response->assertOk();
    $response->assertViewIs('matrix.index');
    $response->assertViewHas('companies');

    $companies = $response->viewData('companies');
    expect($companies)->toBeArray()->not->toBeEmpty();
    $names = array_column($companies, 'name');
    expect($names)->toContain('TestCo');

    deleteTestCsv('TestCo');
});

test('index with company query parameter redirects to show', function (): void {
    $response = get(route('matrix.index') . '?company=TestCo');

    $response->assertRedirect(route('matrix.show', ['company' => 'TestCo']));
});

test('index lists company with image path from Company model when it exists', function (): void {
    $company = Company::factory()->approved()->create(['name' => 'TestCo', 'slug' => 'testco']);
    writeTestCsv('TestCo');

    $response = get(route('matrix.index'));

    $response->assertOk();
    $companies = $response->viewData('companies');
    $match = collect($companies)->firstWhere('name', 'TestCo');
    expect($match)->not->toBeNull();
    expect($match['image_path'])->toBe($company->image_path);

    deleteTestCsv('TestCo');
});

// ─── show ─────────────────────────────────────────────────────────────────────

test('show returns 404 when csv does not exist', function (): void {
    get(route('matrix.show', ['company' => 'nonexistent-company-xyz']))
        ->assertNotFound();
});

test('show displays view with expected data', function (): void {
    writeTestCsv('TestCo');

    $response = get(route('matrix.show', ['company' => 'TestCo']));

    $response->assertOk();
    $response->assertViewIs('matrix.show');

    $response->assertViewHas('company', 'TestCo');
    $response->assertViewHas('rows');
    $response->assertViewHas('columnMaxes');
    $response->assertViewHas('orderedSections');
    $response->assertViewHas('comparisonData');
    $response->assertViewHas('renderCellValue');

    deleteTestCsv('TestCo');
});

test('show rows contain scored categories', function (): void {
    writeTestCsv('TestCo');

    $response = get(route('matrix.show', ['company' => 'TestCo']));
    $rows = $response->viewData('rows');

    expect($rows)->toBeArray()->not->toBeEmpty();

    $testco = collect($rows)->firstWhere('name', 'TestCo');
    expect($testco)->not->toBeNull();
    expect($testco)->toHaveKeys(['features', 'security', 'pricing', 'islPresence', 'totalScore', 'score', 'isBest', 'isSearched', 'logoPath']);

    deleteTestCsv('TestCo');
});

test('show marks the searched company row as isSearched', function (): void {
    writeTestCsv('TestCo');

    $rows = get(route('matrix.show', ['company' => 'TestCo']))->viewData('rows');

    $testco = collect($rows)->firstWhere('name', 'TestCo');
    $altco = collect($rows)->firstWhere('name', 'AltCo');

    expect($testco['isSearched'])->toBeTrue();
    expect($altco['isSearched'])->toBeFalse();

    deleteTestCsv('TestCo');
});

test('show sections are populated from the csv hierarchy', function (): void {
    writeTestCsv('TestCo');

    $sections = get(route('matrix.show', ['company' => 'TestCo']))->viewData('orderedSections');

    $titles = array_column($sections, 'title');
    expect($titles)->toContain('Recommendation and Risk Summary');
    expect($titles)->toContain('Features');
    expect($titles)->toContain('Security and Compliance');
    expect($titles)->toContain('Pricing');
    expect($titles)->toContain('ISL Presence & Ties Assessment');

    // Sub-items come from CSV, not hard-coded
    $featureSection = collect($sections)->firstWhere('title', 'Features');
    expect($featureSection['items'])->toContain('Setup Complexity');
    expect($featureSection['items'])->toContain('AI Services');

    // Recommendation items appear only in their own section
    $recSection = collect($sections)->firstWhere('title', 'Recommendation and Risk Summary');
    expect($recSection['items'])->toContain('Overall Risk Level');
    expect($recSection['items'])->toContain('Recommendation');

    foreach ($sections as $section) {
        if ($section['title'] !== 'Recommendation and Risk Summary') {
            expect($section['items'])->not->toContain('Overall Risk Level');
            expect($section['items'])->not->toContain('Recommendation');
        }
    }

    deleteTestCsv('TestCo');
});

test('show isBest marks the highest-scoring non-searched row', function (): void {
    writeTestCsv('TestCo');

    $rows = get(route('matrix.show', ['company' => 'TestCo']))->viewData('rows');

    $best = collect($rows)->filter(fn ($r): mixed => $r['isBest'])->values();
    expect($best)->toHaveCount(1);
    expect($best[0]['isSearched'])->toBeFalse();

    deleteTestCsv('TestCo');
});

// ─── details ─────────────────────────────────────────────────────────────────

test('details returns 404 when csv does not exist', function (): void {
    get(route('matrix.details', ['alternative' => 'anything', 'company' => 'nonexistent-xyz']))
        ->assertNotFound();
});

test('details displays view with expected data', function (): void {
    writeTestCsv('TestCo');

    $response = get(route('matrix.details', [
        'alternative' => Str::slug('AltCo'),
        'company' => 'TestCo',
    ]));

    $response->assertOk();
    $response->assertViewIs('matrix.details');

    $response->assertViewHas('company', 'TestCo');
    $response->assertViewHas('rows');
    $response->assertViewHas('selected');
    $response->assertViewHas('searchedCompany');
    $response->assertViewHas('orderedSections');
    $response->assertViewHas('comparisonData');
    $response->assertViewHas('renderCellValue');
    $response->assertViewHas('getCellScore');

    $selected = $response->viewData('selected');
    expect($selected['name'])->toBe('AltCo');

    $searchedCompany = $response->viewData('searchedCompany');
    expect($searchedCompany['name'])->toBe('TestCo');

    deleteTestCsv('TestCo');
});

test('details selected is null when alternative does not exist in csv', function (): void {
    writeTestCsv('TestCo');

    $response = get(route('matrix.details', [
        'alternative' => 'ghost-vendor',
        'company' => 'TestCo',
    ]));

    $response->assertOk();
    expect($response->viewData('selected'))->toBeNull();

    deleteTestCsv('TestCo');
});
