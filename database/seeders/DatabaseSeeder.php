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
            //UserSeeder::class,
            //            ResourceSeeder::class,
            //            FundingLevelSeeder::class,
            //            CompanySizeSeeder::class,
            //            CategorySeeder::class,
            //            ExitStrategySeeder::class,
            //            CompanySeeder::class,
            //            AlternativeSeeder::class,
            //            CompanyResourcesSeeder::class,
            //            PersonSeeder::class,
            //            PersonResourcesSeeder::class,
            //            TagSeeder::class,
            //            TaggableSeeder::class,
        ]);
    }
}
