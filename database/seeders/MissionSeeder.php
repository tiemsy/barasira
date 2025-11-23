<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Mission;
use App\Models\User;
use App\Models\Service;

class MissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clients = User::all();       // On suppose que des utilisateurs existent
        $services = Service::all();   // et que des services existent

        if ($clients->isEmpty() || $services->isEmpty()) {
            $this->command->warn('Aucun client ou service trouvé : MissionSeeder ignoré.');
            return;
        }

        foreach ($clients as $client) {
            // Chaque client reçoit entre 1 et 5 missions
            $missionsCount = rand(1, 5);

            $services->random($missionsCount)->each(function ($service) use ($client) {
                Mission::factory()->create([
                    'client_id' => $client->id,
                    'service_id' => $service->id,
                ]);
            });
        }

        $this->command->info('MissionSeeder exécuté avec succès.');
    }
}
