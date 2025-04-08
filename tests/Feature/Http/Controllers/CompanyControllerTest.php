<?php

namespace Tests\Feature\Http\Controllers;

use App\Http\Controllers\CompanyController;
use App\Jobs\AddCompany;
use App\Models\Alternative;
use App\Models\Company;
use App\Models\ExitStrategy;
use App\Models\User;
use App\Notification\ReviewCompany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;

use function Pest\Faker\fake;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

mutates(CompanyController::class);

test('index displays view', function (): void {
    $approvedCompanies = Company::factory()->count(2)->approved()->create();
    $notApprovedCompany = Company::factory()->create();

    $response = get(route('home'));

    $response->assertOk();
    $response->assertViewIs('home');
    $response->assertViewHas('companies');

    $response->assertDontSee($notApprovedCompany->name);
    $response->assertSee($approvedCompanies->first()->name);
});

test('show displays view', function (): void {
    $company = Company::factory()
        ->approved()
        ->create();

    $response = get(route('companies.show', $company));

    $response->assertOk();
    $response->assertViewIs('companies.show');
    $response->assertViewHas('company');

    $response->assertSee($company->name);

    // checks relationships
    $response->assertViewHas('company.founders');
    $response->assertViewHas('company.resources');
    $response->assertViewHas('company.officeLocations');
    $response->assertViewHas('company.tagsRelation');
    $response->assertViewHas('company.investors');
    $response->assertViewHas('company.alternatives');
});

test('company is not shown if no approved', function (): void {
    $company = Company::factory()
        ->create();

    $response = get(route('companies.show', $company));

    $response->assertNotFound();
});

test('store an alternative inside company show', function (): void {
    config()->set('honeypot.enabled', false);

    $company = Company::factory()->approved()->create();

    $alternativeName = fake()->company();
    $alternativeUrl = fake()->url();

    $response = post(route('companies.alternatives.store', $company->slug), [
        'name' => $alternativeName,
        'url' => $alternativeUrl,
    ], [
        'HTTP_REFERER' => route('companies.show', $company->slug),
    ]);

    $this->assertDatabaseHas('alternatives', [
        'name' => $alternativeName,
        'url' => $alternativeUrl,
    ]);

    $this->assertDatabaseHas('alternative_company', [
        'company_id' => $company->id,
        'alternative_id' => Alternative::first()->id,
    ]);

    $response->assertRedirect(route('companies.show', $company->slug));

    $response->assertSessionHas('success', 'Thank you for suggesting an alternative');
});

// test('store saves and redirects', function (): void {
//    $this->withoutExceptionHandling();
//
//    $exit_strategy = ExitStrategy::factory()->create();
//    $name = fake()->name();
//    $slug = fake()->slug();
//    $url = fake()->url();
//    $approved_at = Carbon::parse(fake()->dateTime());
//    $description = fake()->text();
//    $logo = fake()->imageUrl();
//    $notes = fake()->text();
//    $valuation = fake()->numberBetween(-10000, 10000);
//    $exit_valuation = fake()->numberBetween(-10000, 10000);
//    $stock_symbol = fake()->word();
//    $total_funding = fake()->numberBetween(-10000, 10000);
//    $last_funding_date = Carbon::parse(fake()->date());
//    $headquarter = fake()->word();
//    $founded_at = Carbon::parse(fake()->date());
//    $office_locations = json_encode(['areas' => ['full', 'city']]);
//    $employee_count = fake()->numberBetween(-10000, 10000);
//    $stock_quote = fake()->url();
//
//    Queue::fake();
//    Notification::fake();
//
//    $response = post(route('companies.store'), [
//        'exit_strategy_id' => $exit_strategy->id,
//        'name' => $name,
//        'slug' => $slug,
//        'url' => $url,
//        'approved_at' => $approved_at,
//        'description' => $description,
//        'logo' => $logo,
//        'notes' => $notes,
//        'valuation' => $valuation,
//        'exit_valuation' => $exit_valuation,
//        'stock_symbol' => $stock_symbol,
//        'total_funding' => $total_funding,
//        'last_funding_date' => $last_funding_date,
//        'headquarter' => $headquarter,
//        'founded_at' => $founded_at,
//        'office_locations' => $office_locations,
//        'employee_count' => $employee_count,
//        'stock_quote' => $stock_quote,
//    ]);
//
//    $companies = Company::query()
//        ->where('exit_strategy_id', $exit_strategy->id)
//        ->where('name', $name)
//        ->where('slug', $slug)
//        ->where('url', $url)
//        ->where('approved_at', $approved_at)
//        ->where('description', $description)
//        ->where('logo', $logo)
//        ->where('notes', $notes)
//        ->where('valuation', $valuation)
//        ->where('exit_valuation', $exit_valuation)
//        ->where('stock_symbol', $stock_symbol)
//        ->where('total_funding', $total_funding)
//        ->where('last_funding_date', $last_funding_date)
//        ->where('headquarter', $headquarter)
//        ->where('founded_at', $founded_at)
//        ->whereJsonContains('office_locations', $office_locations)
//        ->where('employee_count', $employee_count)
//        ->where('stock_quote', $stock_quote)
//        ->get();
//
//    expect($companies)->toHaveCount(1);
//
//    $company = $companies->first();
//
//    $response->assertRedirect(route('companies.index'));
//    $response->assertSessionHas('company.name', $company->name);
//
//    Queue::assertPushed(AddCompany::class, function ($job) use ($company) {
//        return $job->company->is($company);
//    });
//
//    /** @var User $adminUser */
//    $adminUser = $this->adminUser;
//
//    Notification::assertSentTo($adminUser, ReviewCompany::class, function ($notification) use ($company) {
//        return $notification->company->is($company);
//    });
// });
