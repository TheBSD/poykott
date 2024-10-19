<?php

namespace Database\Factories;

use App\Models\CompanySize;
use Illuminate\Database\Eloquent\Factories\Factory;

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
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->text(),
        ];
    }
}
