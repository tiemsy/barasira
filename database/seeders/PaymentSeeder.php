<?php

namespace Database\Seeders;

use App\Models\Mission;
use App\Models\Payment;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        Mission::query()
            ->where('status', 'completed')
            ->whereNotNull('prestataire_id')
            ->get()
            ->values()
            ->each(function (Mission $mission, int $index) {
                $methods = ['orange_money', 'moov_money', 'carte', 'paypal'];
                $method = $methods[$index % count($methods)];

                Payment::query()->updateOrCreate(
                    ['transaction_id' => 'DEMO-'.$mission->id],
                    [
                        'mission_id' => $mission->id,
                        'payer_id' => $mission->client_id,
                        'receiver_id' => $mission->prestataire_id,
                        'amount' => $mission->price,
                        'status' => 'effectue',
                        'method' => $method,
                        'provider' => $method === 'paypal' ? 'paypal' : 'cinetpay',
                        'paid_at' => $mission->date_end ?? $mission->updated_at,
                    ]
                );
            });

        $this->command->info('Paiements de démonstration créés avec succès.');
    }
}
