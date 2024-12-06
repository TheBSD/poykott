<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->call([
            AdminSeeder::class,

            //            ResourceSeeder::class,
            //            ExitStrategySeeder::class,
            //            CompanySeeder::class,
            //            AlternativeSeeder::class,
            //            PersonSeeder::class,
            //            PersonResourcesSeeder::class,
            //            TagSeeder::class,
            //            TaggableSeeder::class,
        ]);
    }
}
