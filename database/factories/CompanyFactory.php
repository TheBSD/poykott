<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Company;
use App\Models\CompanySize;
use App\Models\ExitStrategy;
use App\Models\FundingLevel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Company>
 */
class CompanyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Company::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'exit_strategy_id' => ExitStrategy::factory(),
            'funding_level_id' => FundingLevel::factory(),
            'company_size_id' => CompanySize::factory(),
            'approved_at' => fake()->dateTime(),
            'name' => fake()->name(),
            'slug' => fake()->slug(),
            'description' => fake()->text(),
            'logo' => fake()->word(),
            'notes' => fake()->text(),
            'valuation' => fake()->numberBetween(-10000, 10000),
            'exit_valuation' => fake()->numberBetween(-10000, 10000),
            'stock_symbol' => fake()->word(),
            'url' => fake()->url(),
            'total_funding' => fake()->numberBetween(-10000, 10000),
            'last_funding_date' => fake()->date(),
            'headquarter' => fake()->word(),
            'founded_at' => fake()->date(),
            'employee_count' => fake()->numberBetween(-10000, 10000),
            'stock_quote' => fake()->url(),
        ];
    }
}
