<?php

namespace Database\Factories;

use App\Models\Mission;
use App\Models\User;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Mission>
 */
class MissionFactory extends Factory
{
    protected $model = Mission::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $statuses = ['pending', 'in_progress', 'completed', 'cancelled'];

        return [
            'client_id' => User::factory(),               // utilisateur client
            'service_id' => Service::factory(),           // service demandé
            'title' => $this->faker->sentence(3),         // titre de la mission
            'description' => $this->faker->paragraph(),   // description détaillée
            'address' => $this->faker->address(),         // adresse complète
            'latitude' => $this->faker->latitude(),       // latitude aléatoire
            'longitude' => $this->faker->longitude(),     // longitude aléatoire
            'status' => $this->faker->randomElement($statuses), // statut de la mission
            'price' => $this->faker->numberBetween(5000, 50000), // prix en CFA
            'date_start' => $this->faker->dateTimeBetween('-1 month', '+1 month'),
            'date_end' => $this->faker->dateTimeBetween('+1 day', '+2 months'),
        ];
    }
}
