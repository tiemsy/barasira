<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\User;
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
            'user_id' => User::where('role', 'prestataire')->inRandomOrder()->first()->id,
            'service_category_id' => ServiceCategory::inRandomOrder()->first()->id ?? ServiceCategory::factory(),

            'city_id' => City::inRandomOrder()->first()->id ?? City::factory(),
            'municipality_id' => null,

            'name' => $this->faker->unique()->jobTitle, // nom de service ex: "Réparation Tuyaux"
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
            'price_min' => $this->faker->numberBetween(3000, 8000),
            'price_max' => $this->faker->numberBetween(9000, 30000),
            'is_active' => $this->faker->boolean(90), // 90% des services actifs
        ];
    }
}
