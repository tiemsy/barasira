<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ServiceCategory;

class ServiceCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Plomberie', 'icon' => 'fa-solid fa-water', 'is_active' => true],
            ['name' => 'Électricité', 'icon' => 'fa-solid fa-bolt', 'is_active' => true],
            ['name' => 'Couture', 'icon' => 'fa-solid fa-scissors', 'is_active' => true],
            ['name' => 'Transport', 'icon' => 'fa-solid fa-truck', 'is_active' => true],
            ['name' => 'Jardinage', 'icon' => 'fa-solid fa-leaf', 'is_active' => true],
            ['name' => 'Ménage', 'icon' => 'fa-solid fa-broom', 'is_active' => true],
            ['name' => 'Informatique', 'icon' => 'fa-solid fa-laptop', 'is_active' => true],
            ['name' => 'Bâtiment', 'icon' => 'fa-solid fa-building', 'is_active' => true],
        ];

        foreach ($categories as $category) {
            ServiceCategory::updateOrCreate(
                ['name' => $category['name']],
                $category
            );
        }

        $this->command->info('Service categories seeded successfully.');
    }
}
