<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceFactory extends Factory
{
    protected $model = Service::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->provider(),
            'service_category_id' => ServiceCategory::factory(),
            'city_id' => City::factory(),
            'municipality_id' => null,
            'name' => 'Dépannage professionnel '.$this->faker->unique()->numerify('###'),
            'description' => 'Diagnostic du besoin, intervention soignée et conseils d’entretien après la prestation.',
            'icon' => 'fa-solid fa-tools',
            'price_min' => 10000,
            'price_max' => 50000,
            'is_active' => true,
        ];
    }
}
