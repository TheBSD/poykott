<?php

namespace Database\Seeders;

use App\Models\SimilarSiteCategory;
use Illuminate\Database\Seeder;

class SimilarSiteCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SimilarSiteCategory::factory()->count(5)->create();
    }
}
