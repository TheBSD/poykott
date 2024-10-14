<?php

namespace Database\Seeders;

use App\Models\FundingLevel;
use Illuminate\Database\Seeder;

class FundingLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        FundingLevel::factory()->count(5)->create();
    }
}
