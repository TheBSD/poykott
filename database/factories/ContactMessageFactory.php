<?php

namespace Database\Factories;

use App\Models\ContactMessage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ContactMessage>
 */
class ContactMessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'message' => fake()->paragraph(3),
            'ip_address' => fake()->ipv4(),
            'read_at' => null,
            'spam_at' => null,
        ];
    }

    /**
     * Mark the contact message as read.
     */
    public function read(): static
    {
        return $this->state(fn (array $attributes): array => [
            'read_at' => now(),
        ]);
    }

    /**
     * Mark the contact message as spam.
     */
    public function spam(): static
    {
        return $this->state(fn (array $attributes): array => [
            'spam_at' => now(),
        ]);
    }
}
