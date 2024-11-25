<?php

namespace Database\Factories;

use App\Models\ExitStrategy;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ExitStrategy>
 */
class ExitStrategyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ExitStrategy::class;

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
