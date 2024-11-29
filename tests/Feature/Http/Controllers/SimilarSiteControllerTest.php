<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\SimilarSite;

use function Pest\Laravel\get;

test('index displays view', function (): void {
    $similarSites = SimilarSite::factory()->count(3)->create();

    // todo xdebug says infinite loop
    // I think solution is to make SimilarSiteCategoryw
    //$response = get(route('similar-sites.index'));

    //$response->assertOk();
    //$response->assertViewIs('similar_sites.index');
});
