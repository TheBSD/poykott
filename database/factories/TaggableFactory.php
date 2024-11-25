<?php

namespace Database\Factories;

use App\Models\Tag;
use App\Models\Taggable;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Taggable>
 */
class TaggableFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Taggable::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'tag_id' => Tag::factory(),
            'taggable_id' => fake()->randomDigitNotNull(),
            'taggable_type' => fake()->word(),
        ];
    }
}
