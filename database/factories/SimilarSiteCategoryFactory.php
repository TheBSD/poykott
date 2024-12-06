<?php

namespace Database\Factories;

use App\Models\SimilarSiteCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class SimilarSiteCategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SimilarSiteCategory::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'description' => $this->faker->text(),
        ];
    }
}
