<?php

namespace Database\Factories;

use App\Enums\ResourceType;
use App\Models\Company;
use App\Models\Person;
use App\Models\Resource;
use Illuminate\Database\Eloquent\Factories\Factory;

class ResourceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Resource::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(4),
            'type' => $this->faker->randomElement(ResourceType::values()),
            'description' => $this->faker->text(),
            'url' => $this->faker->url(),
            'resourceable_id' => $this->faker->numberBetween(1, 3),
            'resourceable_type' => $this->faker->randomElement([
                Person::class,
                Company::class,
            ]),
        ];
    }
}
