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
            $this->call(UserSeeder::class);
            $this->call(UserSkillSeeder::class);
            $this->call(ServiceCategorySeeder::class);
            $this->call(CitySeeder::class);
            $this->call(MunicipalitySeeder::class);
            $this->call(ServiceSeeder::class);
            $this->call(UserSkillSeeder::class);
            $this->call(MissionSeeder::class);
            // $this->call(ResumeSeeder::class);
            $this->call(ReviewSeeder::class);
        }

        if (app()->environment('production')) {
            $this->call(AdminSeeder::class);
        }
    }
}
