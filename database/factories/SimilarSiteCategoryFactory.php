<?php

namespace Database\Factories;

use App\Models\SimilarSiteCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SimilarSiteCategory>
 */
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
            'name' => fake()->name(),
            'description' => fake()->text(),
        ];
    }
}
