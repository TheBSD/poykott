<?php

use App\Console\Commands\SyncCompanyTagsToAlternativesCommand;
use App\Models\Alternative;
use App\Models\Company;
use App\Models\Tag;

test('it syncs company tags to related alternatives', function (): void {
    $tag1 = Tag::factory()->create(['name' => 'Tag 1 for company']);
    $tag2 = Tag::factory()->create(['name' => 'Tag 2 for company']);
    $tag3 = Tag::factory()->create(['name' => 'Tag 3 for alternative 1']);

    $company = Company::factory()->create(['name' => 'Test Company']);
    $company->tagsRelation()->attach([$tag1->id, $tag2->id]);

    $alternative1 = Alternative::factory()->create(['name' => 'Alternative 1']);
    $alternative2 = Alternative::factory()->create(['name' => 'Alternative 2']);
    $alternative2->tagsRelation()->attach([$tag3->id]);

    $company->alternatives()->attach([$alternative1->id, $alternative2->id]);

    expect($alternative1->tagsRelation()->count())->toBe(0);
    expect($alternative2->tagsRelation()->count())->toBe(1);

    $this->artisan(SyncCompanyTagsToAlternativesCommand::class)->assertExitCode(0);

    $alternative1->refresh();
    $alternative2->refresh();

    expect($alternative1->tagsRelation()->count())->toBe(2);
    expect($alternative2->tagsRelation()->count())->toBe(3);
    expect($company->tagsRelation()->count())->toBe(2);

    expect($alternative1->hasTag($tag1))->toBeTrue();
    expect($alternative1->hasTag($tag2))->toBeTrue();
    expect($alternative2->hasTag($tag1))->toBeTrue();
    expect($alternative2->hasTag($tag2))->toBeTrue();
    expect($alternative2->hasTag($tag3))->toBeTrue();
    expect($alternative1->hasTag($tag3))->toBeFalse();
});
