<?php

namespace Database\Factories;

use App\Models\FundingLevel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FundingLevel>
 */
class FundingLevelFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = FundingLevel::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'description' => fake()->text(),
        ];
    }
}
