<?php

namespace Database\Factories;

use App\Models\AiAlternative;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AiAlternative>
 */
class AiAlternativeFactory extends Factory
{
    protected $model = AiAlternative::class;

    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'content' => fake()->sentences(asText: true),
        ];
    }
}
