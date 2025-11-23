<?php

namespace Database\Factories;

use App\Models\ServiceCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ServiceCategory>
 */
class ServiceCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = ServiceCategory::class;

    public function definition(): array
    {
        $categories = [
            ['name' => 'Plomberie', 'icon' => 'fa-solid fa-water'],
            ['name' => 'Électricité', 'icon' => 'fa-solid fa-bolt'],
            ['name' => 'Couture', 'icon' => 'fa-solid fa-scissors'],
            ['name' => 'Transport', 'icon' => 'fa-solid fa-truck'],
            ['name' => 'Jardinage', 'icon' => 'fa-solid fa-leaf'],
            ['name' => 'Ménage', 'icon' => 'fa-solid fa-broom'],
            ['name' => 'Informatique', 'icon' => 'fa-solid fa-laptop'],
            ['name' => 'Bâtiment', 'icon' => 'fa-solid fa-building'],
        ];

        return $this->faker->randomElement($categories);
    }
}
