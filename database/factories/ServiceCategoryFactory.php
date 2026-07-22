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
            ['name' => 'Plomberie', 'description' => 'Installation sanitaire et dépannage.', 'icon' => 'plumbing', 'is_active' => true],
            ['name' => 'Électricité', 'description' => 'Installation et dépannage électrique.', 'icon' => 'electrical', 'is_active' => true],
            ['name' => 'Couture', 'description' => 'Confection et retouche sur mesure.', 'icon' => 'tailoring', 'is_active' => true],
            ['name' => 'Transport', 'description' => 'Transport de personnes et marchandises.', 'icon' => 'transport', 'is_active' => true],
            ['name' => 'Informatique', 'description' => 'Dépannage et assistance numérique.', 'icon' => 'computer', 'is_active' => true],
        ]);
    }
}
