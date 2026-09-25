<?php

use App\Enums\ResourceType;
use App\Models\Company;
use Illuminate\Support\Facades\Storage;

test('exports only companies with a Crunchbase URL', function (): void {
    Storage::fake('local');

    $company = Company::factory()->create();
    $company->resources()->create(['type' => ResourceType::Wikipedia, 'url' => 'https://example.com/wiki']);
    $company->resources()->create(['type' => ResourceType::Crunchbase, 'url' => 'https://www.crunchbase.com/organization/example']);
    Company::factory()->create();

    $output = Storage::disk('local')->path('custom/companies.json');
    $this->artisan('companies:export-crunchbase', ['output' => $output])->assertExitCode(0);

    $data = json_decode((string) Storage::disk('local')->get('custom/companies.json'), true, flags: JSON_THROW_ON_ERROR);

    expect($data)->toHaveCount(1)
        ->and(array_keys($data[0]))->toBe(['id', 'name', 'slug', 'url', 'crunchbase_url'])
        ->and($data[0]['id'])->toBe($company->id)
        ->and($data[0]['crunchbase_url'])->toBe('https://www.crunchbase.com/organization/example');
});
