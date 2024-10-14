<?php

namespace Tests\Feature\Http\Controllers;

use App\Jobs\AddCompany;
use App\Models\Alternative;
use App\Models\User;
use App\Notification\ReviewCompany;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use function Pest\Faker\fake;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

test('create displays view', function (): void {
    $response = get(route('alternatives.create'));

    $response->assertOk();
    $response->assertViewIs('alternative.create');
});


test('store uses form request validation')
    ->assertActionUsesFormRequest(
        \App\Http\Controllers\AlternativeController::class,
        'store',
        \App\Http\Requests\AlternativeStoreRequest::class
    );

test('store saves and redirects', function (): void {
    $name = fake()->name();
    $description = fake()->text();
    $logo = fake()->word();
    $notes = fake()->text();
    $url = fake()->url();

    Queue::fake();
    Notification::fake();

    $response = post(route('alternatives.store'), [
        'name' => $name,
        'description' => $description,
        'logo' => $logo,
        'notes' => $notes,
        'url' => $url,
    ]);

    $alternatives = Alternative::query()
        ->where('name', $name)
        ->where('description', $description)
        ->where('logo', $logo)
        ->where('notes', $notes)
        ->where('url', $url)
        ->get();
    expect($alternatives)->toHaveCount(1);
    $alternative = $alternatives->first();

    $response->assertRedirect(route('company.show', [$alternative->companies]));
    $response->assertSessionHas('alternative.name', $alternative->name);

    Queue::assertPushed(AddCompany::class, function ($job) use ($alternative) {
        return $job->alternative->is($alternative);
    });
    Notification::assertSentTo(User::first(), ReviewCompany::class, function ($notification) use ($alternative) {
        return $notification->alternative->is($alternative);
    });
});
