<?php

namespace Database\Factories;

use App\Models\Alternative;
use Carbon\Carbon;
use DateTime;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Alternative>
 */
class AlternativeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Alternative::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'notes' => fake()->text(),
            'url' => fake()->url(),
        ];
    }

    public function approved(DateTime|Carbon|null $datTime = null): static
    {
        return $this->state(fn (array $attributes): array => [
            'approved_at' => $datTime ?? Carbon::now(),
        ]);
    }
}
