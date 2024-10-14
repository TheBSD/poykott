<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Person;

class PersonFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Person::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'full_name' => $this->faker->word(),
            'avatar' => $this->faker->word(),
            'job_title' => $this->faker->word(),
            'approved_at' => $this->faker->dateTime(),
            'location' => $this->faker->word(),
            'biography' => $this->faker->text(),
            'social_links' => '{}',
        ];
    }
}
