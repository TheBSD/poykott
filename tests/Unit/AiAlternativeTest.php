<?php

use App\Models\AiAlternative;
use App\Models\Company;

test('company ai alternative belongs to company', function (): void {
    $company = Company::factory()->create();
    $aiAlternative = AiAlternative::factory()->create([
        'company_id' => $company->id,
    ]);

    expect($aiAlternative->company)->toBeInstanceOf(Company::class)
        ->and($aiAlternative->company->id)->toBe($company->id);
});

test('company has one ai alternative', function (): void {
    $company = Company::factory()->create();
    $aiAlternative = AiAlternative::factory()->create([
        'company_id' => $company->id,
    ]);

    expect($company->aiAlternative)->toBeInstanceOf(AiAlternative::class)
        ->and($company->aiAlternative->id)->toBe($aiAlternative->id);
});
