<?php

use App\Console\Commands\OfficeLocationsMergerAllCommand;
use App\Models\Company;
use App\Models\OfficeLocation;

test('office locations merger command merges locations with same name and related companies', function (): void {

    $location5621 = OfficeLocation::factory()->create(['id' => 5621, 'name' => 'Paris, France']);
    $companyMenaya = Company::factory()->create(['id' => 1, 'name' => 'Menaya']);
    $location5621->companies()->attach($companyMenaya);

    $location5540 = OfficeLocation::factory()->create(['id' => 5540, 'name' => 'Paris, France']);
    $companyCostockage = Company::factory()->create(['id' => 2, 'name' => 'Costockage']);
    $location5540->companies()->attach($companyCostockage);

    $location7042 = OfficeLocation::factory()->create(['id' => 7042, 'name' => 'Paris, France']);
    $companyCyberArk = Company::factory()->create(['id' => 3, 'name' => 'CyberArk']);
    $location7042->companies()->attach($companyCyberArk);
    $companyCheckmarx = Company::factory()->create(['id' => 4, 'name' => 'Checkmarx']);
    $location7042->companies()->attach($companyCheckmarx);

    $location3861 = OfficeLocation::factory()->create(['id' => 3861, 'name' => 'Paris, France']);
    $companyDynamicYield = Company::factory()->create(['id' => 5, 'name' => 'Dynamic Yield']);
    $location3861->companies()->attach($companyDynamicYield);

    $this->artisan(OfficeLocationsMergerAllCommand::class)->assertExitCode(0);

    $this->assertDatabaseCount('office_locations', 1);
    $this->assertDatabaseCount('companies', 5);
    $this->assertDatabaseHas('office_locations', [
        'id' => 7042,
    ]);
    $this->assertDatabaseHas('company_office_location', [
        'company_id' => $companyMenaya->id,
        'office_location_id' => 7042,
    ]);

    $this->assertDatabaseHas('company_office_location', [
        'company_id' => $companyCostockage->id,
        'office_location_id' => 7042,
    ]);

    $this->assertDatabaseHas('company_office_location', [
        'company_id' => $companyDynamicYield->id,
        'office_location_id' => 7042,
    ]);

    $this->assertDatabaseHas('company_office_location', [
        'company_id' => $companyCyberArk->id,
        'office_location_id' => 7042,
    ]);

    $this->assertDatabaseHas('company_office_location', [
        'company_id' => $companyCheckmarx->id,
        'office_location_id' => 7042,
    ]);
});
