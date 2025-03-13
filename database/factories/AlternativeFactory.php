<?php

namespace Database\Factories;

use App\Models\Alternative;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Alternative>
 */
class AlternativeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Alternative::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'description' => fake()->text(),
            'approved_at' => fake()->dateTime(),
            'notes' => fake()->text(),
            'url' => fake()->url(),
        ];
    }
}
