<?php

use App\Actions\TagsMergerAction;
use App\Models\Alternative;
use App\Models\Company;
use App\Models\Investor;
use App\Models\Person;
use App\Models\Tag;
use Symfony\Component\Console\Output\ConsoleOutput;

it('merges two tags and deletes the "from" tag', function (): void {
    /**
     * Arrange
     */
    $fromTag = Tag::query()->create(['name' => 'Tag A']);
    $toTag = Tag::query()->create(['name' => 'Tag B']);

    $companyA = Company::factory()->create(['name' => 'Company A']);
    $companyB = Company::factory()->create(['name' => 'Company B']);

    $alternativeA = Alternative::factory()->create(['name' => 'Alternative A']);
    $alternativeB = Alternative::factory()->create(['name' => 'Alternative B']);

    $personA = Person::factory()->create(['name' => 'Person A']);
    $personB = Person::factory()->create(['name' => 'Person B']);

    $investorA = Investor::factory()->create(['name' => 'Investor A']);
    $investorB = Investor::factory()->create(['name' => 'Investor B']);

    $fromTag->companies()->attach([$companyA->id]);
    $toTag->companies()->attach([$companyB->id]);

    $fromTag->alternatives()->attach([$alternativeA->id]);
    $toTag->alternatives()->attach([$alternativeB->id]);

    $fromTag->people()->attach([$personA->id]);
    $toTag->people()->attach([$personB->id]);

    $fromTag->investors()->attach([$investorA->id]);
    $toTag->investors()->attach([$investorB->id]);

    /**
     * Assert mock
     */
    $output = $this->createMock(ConsoleOutput::class);
    $output->expects($this->exactly(6))->method('writeln');

    $action = new TagsMergerAction($output);

    /**
     * Act
     */
    $action->execute($fromTag, $toTag);

    /**
     * Assert
     */

    // link companies wih $to
    $this->assertDatabaseHas('taggables', [
        'tag_id' => $toTag->id,
        'taggable_type' => 'company',
        'taggable_id' => $companyA->id,
    ]);

    $this->assertDatabaseHas('taggables', [
        'tag_id' => $toTag->id,
        'taggable_type' => 'company',
        'taggable_id' => $companyB->id,
    ]);

    // unlink companies from $from (if exists)
    $this->assertDatabaseMissing('taggables', [
        'tag_id' => $fromTag->id,
        'taggable_type' => 'company',
        'taggable_id' => $companyA->id,
    ]);

    // link alternatives wih $to
    $this->assertDatabaseHas('taggables', [
        'tag_id' => $toTag->id,
        'taggable_type' => 'alternative',
        'taggable_id' => $alternativeA->id,
    ]);

    $this->assertDatabaseHas('taggables', [
        'tag_id' => $toTag->id,
        'taggable_type' => 'alternative',
        'taggable_id' => $alternativeB->id,
    ]);

    // unlink companies from $from (if exists)
    $this->assertDatabaseMissing('taggables', [
        'tag_id' => $fromTag->id,
        'taggable_type' => 'alternative',
        'taggable_id' => $alternativeA->id,
    ]);

    // link people wih $to
    $this->assertDatabaseHas('taggables', [
        'tag_id' => $toTag->id,
        'taggable_type' => 'person',
        'taggable_id' => $personA->id,
    ]);

    $this->assertDatabaseHas('taggables', [
        'tag_id' => $toTag->id,
        'taggable_type' => 'person',
        'taggable_id' => $personB->id,
    ]);

    // unlink people from $from (if exists)
    $this->assertDatabaseMissing('taggables', [
        'tag_id' => $fromTag->id,
        'taggable_type' => 'person',
        'taggable_id' => $personA->id,
    ]);

    // link investors wih $to
    $this->assertDatabaseHas('taggables', [
        'tag_id' => $toTag->id,
        'taggable_type' => 'investor',
        'taggable_id' => $investorA->id,
    ]);

    $this->assertDatabaseHas('taggables', [
        'tag_id' => $toTag->id,
        'taggable_type' => 'investor',
        'taggable_id' => $investorB->id,
    ]);

    // unlink companies from $from (if exists)
    $this->assertDatabaseMissing('taggables', [
        'tag_id' => $fromTag->id,
        'taggable_type' => 'investor',
        'taggable_id' => $investorA->id,
    ]);

    $this->assertDatabaseCount('taggables', 8);

    // remove $from
    $this->assertDatabaseMissing('tags', ['id' => $fromTag->id]);
    $this->assertDatabaseCount('tags', 1);
});
