<?php

namespace Database\Factories;

use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Service>
 */
class ServiceFactory extends Factory
{
    protected $model = Service::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => ServiceCategory::inRandomOrder()->first()->id ?? ServiceCategory::factory(),
            'name' => $this->faker->unique()->words(2, true), // nom de service ex: "Réparation Tuyaux"
            'description' => $this->faker->paragraph(2),
            'icon' => $this->faker->optional()->randomElement([
                'fa-solid fa-wrench',
                'fa-solid fa-bolt',
                'fa-solid fa-scissors',
                'fa-solid fa-truck',
                'fa-solid fa-leaf',
                'fa-solid fa-broom',
                'fa-solid fa-laptop',
                'fa-solid fa-building'
            ]),
            'price_min' => $this->faker->numberBetween(5000, 20000),
            'price_max' => $this->faker->numberBetween(25000, 100000),
            'is_active' => $this->faker->boolean(90), // 90% des services actifs
        ];
    }
}
