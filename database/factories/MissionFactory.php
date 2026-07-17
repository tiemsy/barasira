<?php

namespace Database\Factories;

use App\Models\Mission;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MissionFactory extends Factory
{
    protected $model = Mission::class;

    public function configure(): static
    {
        return $this->afterCreating(function (Mission $mission) {
            if ($mission->prestataire_id) {
                $mission->service()->update(['user_id' => $mission->prestataire_id]);
            }
        });
    }

    public function definition(): array
    {
        $start = now()->addDays(7)->setHour(9)->startOfHour();

        return [
            'client_id' => User::factory()->client(),
            'prestataire_id' => null,
            'service_id' => Service::factory(),
            'title' => 'Intervention à domicile '.$this->faker->unique()->numerify('###'),
            'description' => 'Le client souhaite une intervention professionnelle, ponctuelle et conforme au besoin décrit.',
            'city' => 'Bamako',
            'skills' => ['Diagnostic', 'Travail soigné'],
            'questions' => ['Le matériel est-il inclus dans le tarif ?'],
            'address' => 'ACI 2000, Bamako',
            'latitude' => 12.6392,
            'longitude' => -8.0029,
            'status' => 'pending',
            'price' => 25000,
            'date_start' => $start,
            'date_end' => $start->copy()->addHours(3),
        ];
    }

    public function assigned(?User $provider = null): static
    {
        return $this->state(fn () => ['prestataire_id' => $provider?->id ?? User::factory()->provider(), 'status' => 'in_progress']);
    }

    public function completed(?User $provider = null): static
    {
        return $this->state(fn () => [
            'prestataire_id' => $provider?->id ?? User::factory()->provider(),
            'status' => 'completed',
            'date_start' => now()->subDays(3)->setHour(9)->startOfHour(),
            'date_end' => now()->subDays(3)->setHour(12)->startOfHour(),
        ]);
    }
}
