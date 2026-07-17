<?php

namespace Database\Factories;

use App\Models\Mission;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'mission_id' => Mission::factory()->completed(),
            'payer_id' => User::factory()->client(),
            'receiver_id' => User::factory()->provider(),
            'amount' => 25000,
            'status' => 'en_attente',
            'method' => 'carte',
            'transaction_id' => 'TEST-'.$this->faker->unique()->numerify('########'),
        ];
    }

    public function completed(): static
    {
        return $this->state(fn () => ['status' => 'effectue']);
    }

    public function refunded(): static
    {
        return $this->state(fn () => ['status' => 'rembourse']);
    }

    public function configure(): static
    {
        return $this->afterCreating(fn (Payment $payment) => $payment->update([
            'payer_id' => $payment->mission->client_id,
            'receiver_id' => $payment->mission->prestataire_id,
            'amount' => $payment->mission->price,
        ]));
    }
}
