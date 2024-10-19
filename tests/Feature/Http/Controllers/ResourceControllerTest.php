<?php

namespace Tests\Feature\Http\Controllers;

use App\Jobs\AddResource;
use App\Models\Resource;
use App\Models\User;
use App\Notification\ReviewResource;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;

use function Pest\Faker\fake;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

test('index displays view', function (): void {
    $resources = Resource::factory()->count(3)->create();

    $response = get(route('resources.index'));

    $response->assertOk();
    $response->assertViewIs('resources.index');
    $response->assertViewHas('resources');
});

test('create displays view', function (): void {
    $response = get(route('resources.create'));

    $response->assertOk();
    $response->assertViewIs('resources.create');
});

test('store uses form request validation')
    ->assertActionUsesFormRequest(
        \App\Http\Controllers\ResourceController::class,
        'store',
        \App\Http\Requests\ResourceStoreRequest::class
    );

test('store saves and redirects', function (): void {
    $title = fake()->sentence(4);
    $type = fake()->word();
    $description = fake()->text();
    $url = fake()->url();

    Queue::fake();
    Notification::fake();

    $response = post(route('resources.store'), [
        'title' => $title,
        'type' => $type,
        'description' => $description,
        'url' => $url,
    ]);

    $resources = Resource::query()
        ->where('title', $title)
        ->where('type', $type)
        ->where('description', $description)
        ->where('url', $url)
        ->get();
    expect($resources)->toHaveCount(1);
    $resource = $resources->first();

    $response->assertSessionHas('resource.title', $resource->title);
    $response->assertRedirect(route('resources.index'));

    Queue::assertPushed(AddResource::class, function ($job) use ($resource) {
        return $job->resource->is($resource);
    });

    /** @var User $adminUser */
    $adminUser = $this->adminUser;

    Notification::assertSentTo($adminUser, ReviewResource::class, function ($notification) use ($resource) {
        return $notification->resource->is($resource);
    });
});
