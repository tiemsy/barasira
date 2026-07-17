<?php

namespace Database\Factories;

use App\Models\ServiceCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceCategoryFactory extends Factory
{
    protected $model = ServiceCategory::class;

    public function definition(): array
    {
        return $this->faker->randomElement([
            ['name' => 'Plomberie', 'description' => 'Installation sanitaire et dépannage.', 'icon' => 'fa-solid fa-water', 'is_active' => true],
            ['name' => 'Électricité', 'description' => 'Installation et dépannage électrique.', 'icon' => 'fa-solid fa-bolt', 'is_active' => true],
            ['name' => 'Couture', 'description' => 'Confection et retouche sur mesure.', 'icon' => 'fa-solid fa-scissors', 'is_active' => true],
            ['name' => 'Transport', 'description' => 'Transport de personnes et marchandises.', 'icon' => 'fa-solid fa-truck', 'is_active' => true],
            ['name' => 'Informatique', 'description' => 'Dépannage et assistance numérique.', 'icon' => 'fa-solid fa-laptop', 'is_active' => true],
        ]);
    }
}
