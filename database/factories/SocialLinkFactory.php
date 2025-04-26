<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Person;
use App\Models\SocialLink;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SocialLink>
 */
class SocialLinkFactory extends Factory
{
    protected $model = SocialLink::class;

    public function definition(): array
    {
        return [
            'url' => fake()->url(),
            'linkable_type' => fake()->randomElement([
                Person::class,
                Company::class,
            ]),
            'linkable_id' => function (array $attributes) {
                if ($attributes['linkable_type'] === Person::class) {
                    return Person::factory()->create()->id;
                }

                return Company::factory()->create()->id;
            },
        ];
    }
}
