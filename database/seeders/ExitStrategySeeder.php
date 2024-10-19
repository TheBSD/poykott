<?php

namespace Database\Seeders;

use App\Models\ExitStrategy;
use Illuminate\Database\Seeder;

class ExitStrategySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ExitStrategy::factory()->count(2)
            ->sequence(
                ['title' => 'IPO'],
                ['title' => 'M&A'],
            )
            ->create();
    }
}
