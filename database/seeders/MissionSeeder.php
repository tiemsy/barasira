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
        $hoursByMission = [
            'Réparer une fuite sous l’évier' => [2, 2.5],
            'Installer un tableau électrique sécurisé' => [6, 6],
            'Confectionner trois tenues en bazin' => [12, 12],
            'Configurer le réseau Wi-Fi du bureau' => [4, 5],
            'Transporter des meubles vers Ségou' => [8, 8],
            'Garder une boutique pendant deux semaines' => [84, 90],
            'Monter un mur de clôture' => [36, 40],
            'Nettoyer des bureaux après travaux' => [8, 9],
            'Préparer une parcelle maraîchère' => [24, 27],
            'Coiffer six personnes pour un mariage' => [8, 9],
            'Réparer une moto en panne' => [3, 3.5],
            'Préparer 80 repas pour une formation' => [16, 18],
            'Installer un kit solaire familial' => [16, 18],
            'Réviser trois climatiseurs' => [6, 7],
            'Confectionner des uniformes de restaurant' => [30, 34],
            'Livrer du matériel de marché' => [5, 5],
            'Électrifier un petit poulailler' => [8, 9],
            'Installer deux ordinateurs de caisse' => [5, 5],
            'Remplacer la plomberie d’une douche' => [8, 8],
            'Nettoyer une maison avant emménagement' => [7, 7],
        ];

        $missions = [
            ['Réparer une fuite sous l’évier', 'aminata.client@BaraSira.test', 'Dépannage plomberie à domicile', 'mariam.plombiere@BaraSira.test', 'completed', 'Le siphon fuit depuis plusieurs jours. Il faut identifier la cause et remplacer les pièces défectueuses.', 'Baco-Djicoroni ACI, Bamako', 'Bamako', 15000, -12, -11, ['Recherche de fuite', 'Installation sanitaire']],
            ['Installer un tableau électrique sécurisé', 'moussa.client@BaraSira.test', 'Installation et dépannage électrique', 'ibrahim.electricien@BaraSira.test', 'in_progress', 'Remplacer l’ancien tableau du bureau et séparer les circuits prises, éclairage et climatisation.', 'Hamdallaye ACI 2000, Bamako', 'Bamako', 65000, 1, 2, ['Tableau électrique', 'Mise aux normes']],
            ['Confectionner trois tenues en bazin', 'fatoumata.client@BaraSira.test', 'Couture traditionnelle et moderne', null, 'pending', 'Confection de trois ensembles en bazin avec prise de mesures et première séance d’essayage.', 'Quinzambougou, Bamako', 'Bamako', 45000, 7, 14, ['Coupe', 'Finitions']],
            ['Configurer le réseau Wi-Fi du bureau', 'moussa.client@BaraSira.test', 'Dépannage informatique et réseau', 'boubacar.informatique@BaraSira.test', 'completed', 'Installer le routeur, sécuriser le réseau et connecter les cinq postes de travail et l’imprimante.', 'Centre commercial de Ségou', 'Ségou', 35000, -35, -34, ['Réseau', 'Sécurité Wi-Fi']],
            ['Transporter des meubles vers Ségou', 'aminata.client@BaraSira.test', 'Transport de marchandises et déménagement', 'oumar.transport@BaraSira.test', 'cancelled', 'Transport d’un salon et de deux armoires depuis Bamako vers Ségou.', 'Badalabougou, Bamako', 'Bamako', 90000, 10, 10, ['Manutention', 'Transport sécurisé']],
            ['Garder une boutique pendant deux semaines', 'adama.client@BaraSira.test', 'Gardiennage de domicile et commerce', 'amadou.gardien@BaraSira.test', 'completed', 'Assurer la surveillance nocturne de la boutique et contrôler les entrées pendant l’absence du propriétaire.', 'Grand marché, Bamako', 'Bamako', 85000, -28, -14, ['Surveillance', 'Contrôle des accès']],
            ['Monter un mur de clôture', 'mamadou.client@BaraSira.test', 'Ouvrier polyvalent et manutention', 'sekou.ouvrier@BaraSira.test', 'completed', 'Construire un mur de clôture de douze mètres, réaliser l’enduit et nettoyer le chantier.', 'Légal Ségou, Kayes', 'Kayes', 180000, -50, -44, ['Maçonnerie', 'Enduit']],
            ['Nettoyer des bureaux après travaux', 'fatoumata.client@BaraSira.test', 'Nettoyage de maison et bureau', 'awa.menage@BaraSira.test', 'completed', 'Grand nettoyage de quatre bureaux après rénovation avec vitres, sols et sanitaires.', 'Faladié, Bamako', 'Bamako', 40000, -22, -21, ['Grand ménage', 'Nettoyage de vitres']],
            ['Préparer une parcelle maraîchère', 'abdoulaye.client@BaraSira.test', 'Entretien maraîcher et travaux agricoles', 'modibo.jardin@BaraSira.test', 'completed', 'Préparer les planches, installer les lignes d’arrosage et effectuer le premier désherbage.', 'Zone Office du Niger, Ségou', 'Ségou', 120000, -65, -60, ['Maraîchage', 'Irrigation']],
            ['Coiffer six personnes pour un mariage', 'rokia.client@BaraSira.test', 'Coiffure et tresses à domicile', 'kadidia.coiffure@BaraSira.test', 'completed', 'Réaliser les coiffures de la mariée et de cinq accompagnantes avant la cérémonie.', 'Wayerma, Sikasso', 'Sikasso', 60000, -18, -17, ['Tresses', 'Coiffure de cérémonie']],
            ['Réparer une moto en panne', 'assetou.client@BaraSira.test', 'Dépannage mécanique auto et moto', 'salif.mecanicien@BaraSira.test', 'completed', 'Diagnostiquer une moto qui ne démarre plus et remplacer les pièces nécessaires.', 'Médine, Sikasso', 'Sikasso', 30000, -9, -9, ['Diagnostic moteur', 'Dépannage moto']],
            ['Préparer 80 repas pour une formation', 'rokia.client@BaraSira.test', 'Traiteur pour cérémonies et entreprises', 'fanta.traiteur@BaraSira.test', 'completed', 'Préparer et livrer quatre-vingts repas avec boissons pour une journée de formation.', 'Hamdallaye, Bamako', 'Bamako', 240000, -40, -39, ['Cuisine malienne', 'Livraison']],
            ['Installer un kit solaire familial', 'mamadou.client@BaraSira.test', 'Installation de kits solaires', 'youssouf.solaire@BaraSira.test', 'completed', 'Installer quatre panneaux, une batterie et l’éclairage de la maison avec protections.', 'Khasso, Kayes', 'Kayes', 325000, -75, -72, ['Panneaux solaires', 'Batteries']],
            ['Réviser trois climatiseurs', 'moussa.client@BaraSira.test', 'Entretien climatisation et froid', 'nana.climatisation@BaraSira.test', 'completed', 'Nettoyer et contrôler trois climatiseurs du bureau avant la saison chaude.', 'ACI 2000, Bamako', 'Bamako', 75000, -31, -30, ['Climatisation', 'Entretien']],
            ['Confectionner des uniformes de restaurant', 'assetou.client@BaraSira.test', 'Couture traditionnelle et moderne', 'aissata.couture@BaraSira.test', 'completed', 'Confectionner huit tabliers et huit chemises assorties pour le personnel du restaurant.', 'Niaréla, Bamako', 'Bamako', 110000, -55, -48, ['Coupe', 'Uniformes']],
            ['Livrer du matériel de marché', 'adama.client@BaraSira.test', 'Transport de marchandises et déménagement', 'oumar.transport@BaraSira.test', 'in_progress', 'Transporter des cartons et deux congélateurs depuis l’entrepôt jusqu’à la boutique.', 'Sogoniko, Bamako', 'Bamako', 55000, 0, 1, ['Transport', 'Manutention']],
            ['Électrifier un petit poulailler', 'abdoulaye.client@BaraSira.test', 'Installation et dépannage électrique', 'ibrahim.electricien@BaraSira.test', 'completed', 'Installer l’éclairage, deux prises et les protections électriques du poulailler.', 'Pelengana, Ségou', 'Ségou', 70000, -26, -24, ['Installation électrique', 'Éclairage']],
            ['Installer deux ordinateurs de caisse', 'rokia.client@BaraSira.test', 'Dépannage informatique et réseau', null, 'pending', 'Configurer deux ordinateurs, les imprimantes de reçus et la sauvegarde des ventes.', 'Wolofobougou, Bamako', 'Bamako', 50000, 5, 6, ['Installation', 'Sauvegarde']],
            ['Remplacer la plomberie d’une douche', 'mamadou.client@BaraSira.test', 'Dépannage plomberie à domicile', 'mariam.plombiere@BaraSira.test', 'in_progress', 'Remplacer la tuyauterie, le mélangeur et vérifier l’évacuation de la douche.', 'Kayes N’Di, Kayes', 'Kayes', 80000, 2, 4, ['Plomberie', 'Évacuation']],
            ['Nettoyer une maison avant emménagement', 'aminata.client@BaraSira.test', 'Nettoyage de maison et bureau', 'awa.menage@BaraSira.test', 'cancelled', 'Nettoyage complet d’une maison de quatre pièces avant installation de la famille.', 'Kalaban Coura, Bamako', 'Bamako', 35000, 8, 8, ['Nettoyage', 'Désinfection']],
        ];

        $coordinates = [
            'Bamako' => [12.6392, -8.0029],
            'Ségou' => [13.4317, -6.2157],
            'Sikasso' => [11.3176, -5.6665],
            'Kayes' => [14.4469, -11.4447],
        ];

        foreach ($missions as [$title, $clientEmail, $serviceName, $providerEmail, $status, $description, $address, $city, $price, $startDay, $endDay, $skills]) {
            Mission::query()->updateOrCreate(['title' => $title], [
                'client_id' => User::query()->where('email', $clientEmail)->value('id'),
                'prestataire_id' => $providerEmail ? User::query()->where('email', $providerEmail)->value('id') : null,
                'service_id' => Service::query()->where('name', $serviceName)->value('id'),
                'description' => $description,
                'city' => $city,
                'address' => $address,
                'latitude' => $coordinates[$city][0],
                'longitude' => $coordinates[$city][1],
                'status' => $status,
                'price' => $price,
                'initial_hours' => $hoursByMission[$title][0],
                'billable_hours' => $hoursByMission[$title][1],
                'date_start' => now()->startOfDay()->addDays($startDay)->setHour(9),
                'date_end' => now()->startOfDay()->addDays($endDay)->setHour(12),
                'skills' => $skills,
                'questions' => ['Pouvez-vous confirmer votre disponibilité ?', 'Le matériel est-il inclus dans le tarif ?'],
            ]);
        }

        $this->command->info('Missions de démonstration créées avec succès.');
    }
}
