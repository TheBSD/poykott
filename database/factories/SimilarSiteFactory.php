<?php

namespace Database\Factories;

use App\Models\SimilarSite;
use App\Models\SimilarSiteCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class SimilarSiteFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SimilarSite::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'similar_site_category_id' => SimilarSiteCategory::factory(),
            'name' => $this->faker->name(),
            'url' => $this->faker->url(),
            'description' => $this->faker->text(),
        ];
    }
}
