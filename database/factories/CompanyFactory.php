<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Company;
use App\Models\CompanySize;
use App\Models\ExitStrategy;
use App\Models\FundingLevel;
use Illuminate\Database\Eloquent\Factories\Factory;

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
            'approved_at' => $this->faker->dateTime(),
            'name' => $this->faker->name(),
            'slug' => $this->faker->slug(),
            'description' => $this->faker->text(),
            'logo' => $this->faker->word(),
            'notes' => $this->faker->text(),
            'valuation' => $this->faker->numberBetween(-10000, 10000),
            'exit_valuation' => $this->faker->numberBetween(-10000, 10000),
            'stock_symbol' => $this->faker->word(),
            'url' => $this->faker->url(),
            'total_funding' => $this->faker->numberBetween(-10000, 10000),
            'last_funding_date' => $this->faker->date(),
            'headquarter' => $this->faker->word(),
            'founded_at' => $this->faker->date(),
            'office_locations' => '{}',
            'employee_count' => $this->faker->numberBetween(-10000, 10000),
            'stock_quote' => $this->faker->url(),
        ];
    }
}
