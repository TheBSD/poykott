<?php

namespace Database\Factories;

use App\Models\Person;
use App\Models\PersonResources;
use Illuminate\Database\Eloquent\Factories\Factory;

class PersonResourcesFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PersonResources::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'person_id' => Person::factory(),
            'title' => $this->faker->sentence(4),
            'url' => $this->faker->url(),
        ];
    }
}
