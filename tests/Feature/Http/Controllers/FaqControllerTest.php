<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Faq;

use function Pest\Laravel\get;

test('invoke displays view', function (): void {
    $faqs = Faq::factory()->count(3)->create();

    $response = get(route('faqs'));

    $response->assertOk();
    $response->assertViewIs('pages.faqs');
    $response->assertViewHas('faqs');
    $response->assertViewHasAll(['faqs' => $faqs]);
});
