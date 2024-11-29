<?php

namespace Database\Seeders;

use App\Models\SimilarSite;
use Illuminate\Database\Seeder;

class SimilarSiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SimilarSite::factory()->count(5)->create();
    }
}
