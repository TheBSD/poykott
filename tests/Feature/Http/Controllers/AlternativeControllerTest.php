<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Alternative;
use App\Models\Company;
use App\Notification\ReviewAlternative;
use Illuminate\Support\Facades\Notification;

use function Pest\Faker\fake;
use function Pest\Laravel\post;

test('store saves and redirects', function (): void {
    Notification::fake();

    // Arrange
    $company = Company::factory()->approved()->create();
    $name = fake()->company();
    $url = fake()->url();

    // Act
    $response = post(route('companies.alternatives.store', $company->slug), [
        'name' => $name,
        'url' => $url,
    ], [
        'HTTP_REFERER' => route('companies.show', $company->slug),
    ]);

    // Assert
    $alternatives = Alternative::query()
        ->whereRelation('companies', 'id', $company->id)
        ->where('name', $name)
        ->where('url', $url)
        ->get();

    expect($alternatives)->toHaveCount(1);

    $alternative = $alternatives->first();

    $this->assertDatabaseHas('alternatives', [
        'name' => $name,
        'url' => $url,
    ]);

    $this->assertDatabaseHas('alternative_company', [
        'company_id' => $company->id,
        'alternative_id' => $alternative->id,
    ]);

    $response->assertRedirect(route('companies.show', $company->slug));
    $response->assertSessionHas('success', 'Thank you for suggesting an alternative');

    /** @var User $adminUser * */
    $adminUser = $this->adminUser;

    Notification::assertSentTo(
        $adminUser,
        ReviewAlternative::class,
        function ($notification) use ($alternative, $company): bool {
            return $notification->alternative->is($alternative)
                && $notification->company->is($company);
        });
});
