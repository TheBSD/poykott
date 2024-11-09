<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\OfficeLocation;
use Illuminate\Database\Eloquent\Factories\Factory;

class OfficeLocationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = OfficeLocation::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'lat' => $this->faker->latitude(),
            'lng' => $this->faker->longitude(),
            'company_id' => Company::factory(),
        ];
    }
}
