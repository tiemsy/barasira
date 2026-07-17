<?php

namespace Database\Seeders;

use App\Models\Mission;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $missions = Mission::query()
            ->where('status', 'completed')
            ->whereNotNull('prestataire_id')
            ->get();

        if ($missions->isEmpty()) {
            $this->command->warn('Aucune mission terminée et affectée : aucun avis créé.');

            return;
        }

        $feedback = [
            'Réparer une fuite sous l’évier' => [5, 'Intervention rapide et très propre. La fuite est réparée et Mariam a pris le temps de m’expliquer le problème.'],
            'Configurer le réseau Wi-Fi du bureau' => [4, 'Configuration efficace et conseils utiles pour sécuriser notre réseau. Toute l’équipe peut maintenant travailler correctement.'],
        ];

        foreach ($missions as $mission) {
            [$rating, $comment] = $feedback[$mission->title] ?? [5, 'Prestataire ponctuel, professionnel et travail conforme à la demande.'];

            Review::query()->updateOrCreate(
                [
                    'mission_id' => $mission->id,
                    'reviewer_id' => $mission->client_id,
                ],
                [
                    'reviewed_id' => $mission->prestataire_id,
                    'rating' => $rating,
                    'comment' => $comment,
                ]
            );
        }

        Review::query()
            ->selectRaw('reviewed_id, AVG(rating) as average_rating')
            ->groupBy('reviewed_id')
            ->get()
            ->each(fn (Review $review) => User::query()
                ->whereKey($review->reviewed_id)
                ->update(['rating' => round((float) $review->average_rating, 2)]));

        $this->command->info('ReviewSeeder exécuté avec succès.');
    }
}
