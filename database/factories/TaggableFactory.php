<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Tag;
use App\Models\Taggable;

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
            'taggable_id' => $this->faker->randomDigitNotNull(),
            'taggable_type' => $this->faker->word(),
        ];
    }
}
