<?php

namespace Database\Seeders;

use App\Models\CompanyResources;
use Illuminate\Database\Seeder;

class CompanyResourcesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CompanyResources::factory()->count(5)->create();
    }
}
