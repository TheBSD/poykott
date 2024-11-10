<?php

namespace Database\Factories;

use App\Models\Person;
use Illuminate\Database\Eloquent\Factories\Factory;

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
            'name' => $this->faker->word(),
            'avatar' => $this->faker->word(),
            'job_title' => $this->faker->word(),
            'approved_at' => $this->faker->dateTime(),
            'location' => $this->faker->word(),
            'biography' => $this->faker->text(),
            'social_links' => '{}',
        ];
    }
}
