<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\CompanyResources;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyResourcesFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CompanyResources::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'title' => $this->faker->sentence(4),
            'url' => $this->faker->url(),
        ];
    }
}
