<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Review;
use App\Models\Mission;
use App\Models\User;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $missions = Mission::all();

        if ($users->count() < 2 || $missions->isEmpty()) {
            $this->command->warn('Pas assez d’utilisateurs ou de missions pour créer des avis.');
            return;
        }

        // Chaque mission peut recevoir 1 à 3 avis
        foreach ($missions as $mission) {
            $reviewCount = rand(1, 3);

            for ($i = 0; $i < $reviewCount; $i++) {
                $reviewer = $users->random();
                do {
                    $reviewed = $users->random();
                } while ($reviewed->id === $reviewer->id);

                Review::factory()->create([
                    'mission_id' => $mission->id,
                    'reviewer_id' => $reviewer->id,
                    'reviewed_id' => $reviewed->id,
                ]);
            }
        }

        $this->command->info('ReviewSeeder exécuté avec succès.');
    }
}
