<?php

namespace Database\Seeders;

use App\Models\PlatformReview;
use App\Models\User;
use Illuminate\Database\Seeder;

class PlatformReviewSeeder extends Seeder
{
    public function run(): void
    {
        $reviews = [
            ['aminata.client@BaraSira.test', 5, 'BaraSira m’a permis de trouver rapidement une plombière sérieuse près de chez moi. La prise de contact est simple et rassurante.', 3],
            ['moussa.client@BaraSira.test', 4, 'La plateforme est pratique pour publier les besoins de mon entreprise et suivre les échanges avec les prestataires au même endroit.', 6],
            ['rokia.client@BaraSira.test', 5, 'J’apprécie la diversité des métiers proposés et les profils détaillés. J’ai trouvé de bons professionnels pour nos événements.', 9],
            ['ibrahim.electricien@BaraSira.test', 5, 'BaraSira me donne davantage de visibilité et me permet de recevoir des demandes précises de clients dans ma zone.', 12],
            ['mariam.plombiere@BaraSira.test', 4, 'Les missions sont claires et la messagerie facilite beaucoup l’organisation avec les clients avant une intervention.', 15],
            ['boubacar.informatique@BaraSira.test', 5, 'Une solution locale utile pour présenter mes services, valoriser mon expérience et développer mon activité professionnelle.', 18],
        ];

        foreach ($reviews as [$email, $rating, $comment, $daysAgo]) {
            $user = User::query()->where('email', $email)->first();

            if (! $user) {
                continue;
            }

            PlatformReview::query()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'rating' => $rating,
                    'comment' => $comment,
                    'is_published' => true,
                    'created_at' => now()->subDays($daysAgo),
                    'updated_at' => now()->subDays($daysAgo),
                ]
            );
        }

        $this->command?->info('Avis sur la plateforme BaraSira créés avec succès.');
    }
}
