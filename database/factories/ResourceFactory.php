<?php

namespace Database\Factories;

use App\Enums\ResourceType;
use App\Models\Company;
use App\Models\Person;
use App\Models\Resource;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Resource>
 */
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
            'type' => fake()->randomElement(ResourceType::values()),
            'notes' => fake()->text(),
            'url' => fake()->url(),
            'resourceable_id' => fake()->numberBetween(1, 3),
            'resourceable_type' => fake()->randomElement([
                Person::class,
                Company::class,
            ]),
        ];
    }
}
