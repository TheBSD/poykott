<?php

use App\Actions\OfficeLocationsMergerAction;
use App\Models\Company;
use App\Models\OfficeLocation;
use Symfony\Component\Console\Output\ConsoleOutput;

it('merges two office locations and deletes the "from" location', function (): void {
    /**
     * Arrange
     */
    $fromLocation = OfficeLocation::query()->create(['name' => 'Location A']);
    $toLocation = OfficeLocation::query()->create(['name' => 'Location B']);

    $companyA = Company::factory()->create(['name' => 'Company A']);
    $companyB = Company::factory()->create(['name' => 'Company B']);
    $companyC = Company::factory()->create(['name' => 'Company C']);

    $fromLocation->companies()->attach([$companyA->id, $companyB->id]);
    $toLocation->companies()->attach([$companyC->id]);

    /**
     * Assert mock
     */
    $output = $this->createMock(ConsoleOutput::class);
    $output->expects($this->exactly(3))->method('writeln');

    $action = new OfficeLocationsMergerAction($output);

    /**
     * Act
     */
    $action->execute($fromLocation, $toLocation);

    /**
     * Assert
     */

    // link now wih $to
    $this->assertDatabaseHas('company_office_location', [
        'company_id' => $companyA->id,
        'office_location_id' => $toLocation->id,
    ]);

    $this->assertDatabaseHas('company_office_location', [
        'company_id' => $companyB->id,
        'office_location_id' => $toLocation->id,
    ]);

    $this->assertDatabaseHas('company_office_location', [
        'company_id' => $companyC->id,
        'office_location_id' => $toLocation->id,
    ]);

    // unlink from $from (if exists)
    $this->assertDatabaseMissing('company_office_location', [
        'company_id' => $companyA->id,
        'office_location_id' => $fromLocation->id,
    ]);

    $this->assertDatabaseMissing('company_office_location', [
        'company_id' => $companyB->id,
        'office_location_id' => $fromLocation->id,
    ]);

    $this->assertDatabaseMissing('company_office_location', [
        'company_id' => $companyC->id,
        'office_location_id' => $fromLocation->id,
    ]);

    // remove $from
    $this->assertDatabaseMissing('office_locations', ['id' => $fromLocation->id]);
});
