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
            ->each(function (Mission $mission) {
                Payment::query()->updateOrCreate(
                    ['transaction_id' => 'DEMO-'.$mission->id],
                    [
                        'mission_id' => $mission->id,
                        'payer_id' => $mission->client_id,
                        'receiver_id' => $mission->prestataire_id,
                        'amount' => $mission->price,
                        'status' => 'effectue',
                        'method' => 'carte',
                    ]
                );
            });

        $this->command->info('Paiements de démonstration créés avec succès.');
    }
}
