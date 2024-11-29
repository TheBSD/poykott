<?php

namespace Database\Factories;

use App\Models\Company;
use Carbon\Carbon;
use DateTime;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Company>
 */
class CompanyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Company::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {

        $name = $this->faker->company();

        return [
            'name' => $name,
            'url' => $this->faker->url(),
            'slug' => Str::slug($name),
        ];
    }

    public function approved(DateTime|Carbon|null $datTime = null): static
    {
        return $this->state(fn (array $attributes) => [
            'approved_at' => $datTime ?? Carbon::now(),
        ]);
    }
}
