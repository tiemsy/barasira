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
        if (app()->environment('local') || app()->environment('staging')) {
            $this->call([
                UserSeeder::class,
                ServiceCategorySeeder::class,
                CitySeeder::class,
                MunicipalitySeeder::class,
                ServiceSeeder::class,
                UserSkillSeeder::class,
                ResumeSeeder::class,
                MissionSeeder::class,
                ReviewSeeder::class,
                PaymentSeeder::class,
            ]);
        }

        if (app()->environment('production')) {
            $this->call(AdminSeeder::class);
        }

        if (app()->environment('local', 'staging', 'production')) {
            $this->call(SuperAdminSeeder::class);
        }
    }
}
