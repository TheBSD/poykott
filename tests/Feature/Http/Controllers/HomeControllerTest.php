<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Alternative;

use function Pest\Laravel\get;

test('show alternative displays view', function (): void {
    $this->withoutExceptionHandling();
    $alternative = Alternative::factory()
        ->approved()
        ->create();

    $response = get(route('alternatives.show', $alternative));

    $response->assertOk();
    $response->assertViewIs('alternatives.show');
    $response->assertViewHas('alternative');

    $response->assertSee($alternative->name);

    // checks relationships
    $response->assertViewHas('alternative.resources');
    $response->assertViewHas('alternative.tagsRelation');
    $response->assertViewHas('alternative.companies');
});
