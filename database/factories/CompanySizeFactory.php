<?php

namespace Database\Factories;

use App\Models\CompanySize;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CompanySize>
 */
class CompanySizeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CompanySize::class;

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
