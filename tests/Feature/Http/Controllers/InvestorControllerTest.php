<?php

namespace Tests\Feature\Http\Controllers;

use App\Jobs\AddInvestor;
use App\Models\Investor;
use App\Models\User;
use App\Notification\ReviewInvestor;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;

use function Pest\Faker\fake;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

test('index displays view', function (): void {
    // Investor::factory()->count(3)->create();
    //
    // $response = get(route('investors.index'));
    //
    // $response->assertOk();
    // $response->assertViewIs('investors.index');
    // $response->assertViewHas('investors');
});

// test('store uses form request validation')
//    ->assertActionUsesFormRequest(
//        \App\Http\Controllers\InvestorController::class,
//        'store',
//        \App\Http\Requests\InvestorStoreRequest::class
//    );

test('store saves and redirects', function (): void {
    // $name = fake()->name();
    // $description = fake()->text();
    // $url = fake()->url();
    // $logo = fake()->word();
    //
    // Queue::fake();
    // Notification::fake();
    //
    // $response = post(route('investors.store'), [
    //    'name' => $name,
    //    'description' => $description,
    //    'url' => $url,
    //    'logo' => $logo,
    // ]);
    //
    // $investors = Investor::query()
    //    ->where('name', $name)
    //    ->where('description', $description)
    //    ->where('url', $url)
    //    ->where('logo', $logo)
    //    ->get();
    //
    // expect($investors)->toHaveCount(1);
    // $investor = $investors->first();
    //
    // $response->assertRedirect(route('investors.index'));
    // $response->assertSessionHas('investor.name', $investor->name);
    //
    // Queue::assertPushed(AddInvestor::class, function ($job) use ($investor) {
    //    return $job->investor->is($investor);
    // });
    //
    // /** @var User $adminUser */
    // $adminUser = $this->adminUser;
    // Notification::assertSentTo($adminUser, ReviewInvestor::class, function ($notification) use ($investor) {
    //    return $notification->investor->is($investor);
    // });
});
