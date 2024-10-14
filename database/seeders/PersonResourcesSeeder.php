<?php

namespace Database\Seeders;

use App\Models\PersonResources;
use Illuminate\Database\Seeder;

class PersonResourcesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PersonResources::factory()->count(5)->create();
    }
}
