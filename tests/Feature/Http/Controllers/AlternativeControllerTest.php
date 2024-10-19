<?php

namespace Tests\Feature\Http\Controllers;

use App\Jobs\AddAlternative;
use App\Models\Alternative;
use App\Models\User;
use App\Notification\ReviewAlternative;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;

use function Pest\Faker\fake;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

test('create displays view', function (): void {
    $response = get(route('alternatives.create'));

    $response->assertOk();
    $response->assertViewIs('alternatives.create');
});

test('store uses form request validation', function (): void {
    $this->assertActionUsesFormRequest(
        \App\Http\Controllers\AlternativeController::class,
        'store',
        \App\Http\Requests\AlternativeStoreRequest::class
    );
});

test('store saves and redirects', function (): void {

    $name = fake()->name();
    $description = fake()->text();
    $notes = fake()->text();
    $url = fake()->url();
    $logo = fake()->imageUrl();

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

    $response->assertRedirect(route('welcome'));
    $response->assertSessionHas('alternative.name', $alternative->name);

    Queue::assertPushed(AddAlternative::class, function ($job) use ($alternative) {
        return $job->alternative->is($alternative);
    });

    /* @var User $adminUser */
    $adminUser = $this->adminUser;

    Notification::assertSentTo($adminUser, ReviewAlternative::class, function ($notification) use ($alternative) {
        return $notification->alternative->is($alternative);
    });
});
