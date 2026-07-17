<?php

namespace Database\Seeders;

use App\Models\Mission;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;

class MissionSeeder extends Seeder
{
    public function run(): void
    {
        $missions = [
            ['Réparer une fuite sous l’évier', 'aminata.client@barasira.test', 'Dépannage plomberie à domicile', 'mariam.plombiere@barasira.test', 'completed', 'Le siphon fuit depuis plusieurs jours. Il faut identifier la cause et remplacer les pièces défectueuses.', 'Baco-Djicoroni ACI, Bamako', 'Bamako', 15000, -12, -11, ['Recherche de fuite', 'Installation sanitaire']],
            ['Installer un tableau électrique sécurisé', 'moussa.client@barasira.test', 'Installation et dépannage électrique', 'ibrahim.electricien@barasira.test', 'in_progress', 'Remplacer l’ancien tableau du bureau et séparer les circuits prises, éclairage et climatisation.', 'Hamdallaye ACI 2000, Bamako', 'Bamako', 65000, 1, 2, ['Tableau électrique', 'Mise aux normes']],
            ['Confectionner trois tenues en bazin', 'fatoumata.client@barasira.test', 'Couture traditionnelle et moderne', null, 'pending', 'Confection de trois ensembles en bazin avec prise de mesures et première séance d’essayage.', 'Quinzambougou, Bamako', 'Bamako', 45000, 7, 14, ['Coupe', 'Finitions']],
            ['Configurer le réseau Wi-Fi du bureau', 'moussa.client@barasira.test', 'Dépannage informatique et réseau', 'boubacar.informatique@barasira.test', 'completed', 'Installer le routeur, sécuriser le réseau et connecter les cinq postes de travail et l’imprimante.', 'Centre commercial de Ségou', 'Ségou', 35000, -35, -34, ['Réseau', 'Sécurité Wi-Fi']],
            ['Transporter des meubles vers Ségou', 'aminata.client@barasira.test', 'Transport de marchandises et déménagement', 'oumar.transport@barasira.test', 'cancelled', 'Transport d’un salon et de deux armoires depuis Bamako vers Ségou.', 'Badalabougou, Bamako', 'Bamako', 90000, 10, 10, ['Manutention', 'Transport sécurisé']],
        ];

        foreach ($missions as [$title, $clientEmail, $serviceName, $providerEmail, $status, $description, $address, $city, $price, $startDay, $endDay, $skills]) {
            Mission::query()->updateOrCreate(['title' => $title], [
                'client_id' => User::query()->where('email', $clientEmail)->value('id'),
                'prestataire_id' => $providerEmail ? User::query()->where('email', $providerEmail)->value('id') : null,
                'service_id' => Service::query()->where('name', $serviceName)->value('id'),
                'description' => $description,
                'city' => $city,
                'address' => $address,
                'latitude' => $city === 'Ségou' ? 13.4317 : 12.6392,
                'longitude' => $city === 'Ségou' ? -6.2157 : -8.0029,
                'status' => $status,
                'price' => $price,
                'date_start' => now()->startOfDay()->addDays($startDay)->setHour(9),
                'date_end' => now()->startOfDay()->addDays($endDay)->setHour(12),
                'skills' => $skills,
                'questions' => ['Pouvez-vous confirmer votre disponibilité ?', 'Le matériel est-il inclus dans le tarif ?'],
            ]);
        }

        $this->command->info('Missions de démonstration créées avec succès.');
    }
}
