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
            ['Garder une boutique pendant deux semaines', 'adama.client@barasira.test', 'Gardiennage de domicile et commerce', 'amadou.gardien@barasira.test', 'completed', 'Assurer la surveillance nocturne de la boutique et contrôler les entrées pendant l’absence du propriétaire.', 'Grand marché, Bamako', 'Bamako', 85000, -28, -14, ['Surveillance', 'Contrôle des accès']],
            ['Monter un mur de clôture', 'mamadou.client@barasira.test', 'Ouvrier polyvalent et manutention', 'sekou.ouvrier@barasira.test', 'completed', 'Construire un mur de clôture de douze mètres, réaliser l’enduit et nettoyer le chantier.', 'Légal Ségou, Kayes', 'Kayes', 180000, -50, -44, ['Maçonnerie', 'Enduit']],
            ['Nettoyer des bureaux après travaux', 'fatoumata.client@barasira.test', 'Nettoyage de maison et bureau', 'awa.menage@barasira.test', 'completed', 'Grand nettoyage de quatre bureaux après rénovation avec vitres, sols et sanitaires.', 'Faladié, Bamako', 'Bamako', 40000, -22, -21, ['Grand ménage', 'Nettoyage de vitres']],
            ['Préparer une parcelle maraîchère', 'abdoulaye.client@barasira.test', 'Entretien maraîcher et travaux agricoles', 'modibo.jardin@barasira.test', 'completed', 'Préparer les planches, installer les lignes d’arrosage et effectuer le premier désherbage.', 'Zone Office du Niger, Ségou', 'Ségou', 120000, -65, -60, ['Maraîchage', 'Irrigation']],
            ['Coiffer six personnes pour un mariage', 'rokia.client@barasira.test', 'Coiffure et tresses à domicile', 'kadidia.coiffure@barasira.test', 'completed', 'Réaliser les coiffures de la mariée et de cinq accompagnantes avant la cérémonie.', 'Wayerma, Sikasso', 'Sikasso', 60000, -18, -17, ['Tresses', 'Coiffure de cérémonie']],
            ['Réparer une moto en panne', 'assetou.client@barasira.test', 'Dépannage mécanique auto et moto', 'salif.mecanicien@barasira.test', 'completed', 'Diagnostiquer une moto qui ne démarre plus et remplacer les pièces nécessaires.', 'Médine, Sikasso', 'Sikasso', 30000, -9, -9, ['Diagnostic moteur', 'Dépannage moto']],
            ['Préparer 80 repas pour une formation', 'rokia.client@barasira.test', 'Traiteur pour cérémonies et entreprises', 'fanta.traiteur@barasira.test', 'completed', 'Préparer et livrer quatre-vingts repas avec boissons pour une journée de formation.', 'Hamdallaye, Bamako', 'Bamako', 240000, -40, -39, ['Cuisine malienne', 'Livraison']],
            ['Installer un kit solaire familial', 'mamadou.client@barasira.test', 'Installation de kits solaires', 'youssouf.solaire@barasira.test', 'completed', 'Installer quatre panneaux, une batterie et l’éclairage de la maison avec protections.', 'Khasso, Kayes', 'Kayes', 325000, -75, -72, ['Panneaux solaires', 'Batteries']],
            ['Réviser trois climatiseurs', 'moussa.client@barasira.test', 'Entretien climatisation et froid', 'nana.climatisation@barasira.test', 'completed', 'Nettoyer et contrôler trois climatiseurs du bureau avant la saison chaude.', 'ACI 2000, Bamako', 'Bamako', 75000, -31, -30, ['Climatisation', 'Entretien']],
            ['Confectionner des uniformes de restaurant', 'assetou.client@barasira.test', 'Couture traditionnelle et moderne', 'aissata.couture@barasira.test', 'completed', 'Confectionner huit tabliers et huit chemises assorties pour le personnel du restaurant.', 'Niaréla, Bamako', 'Bamako', 110000, -55, -48, ['Coupe', 'Uniformes']],
            ['Livrer du matériel de marché', 'adama.client@barasira.test', 'Transport de marchandises et déménagement', 'oumar.transport@barasira.test', 'in_progress', 'Transporter des cartons et deux congélateurs depuis l’entrepôt jusqu’à la boutique.', 'Sogoniko, Bamako', 'Bamako', 55000, 0, 1, ['Transport', 'Manutention']],
            ['Électrifier un petit poulailler', 'abdoulaye.client@barasira.test', 'Installation et dépannage électrique', 'ibrahim.electricien@barasira.test', 'completed', 'Installer l’éclairage, deux prises et les protections électriques du poulailler.', 'Pelengana, Ségou', 'Ségou', 70000, -26, -24, ['Installation électrique', 'Éclairage']],
            ['Installer deux ordinateurs de caisse', 'rokia.client@barasira.test', 'Dépannage informatique et réseau', null, 'pending', 'Configurer deux ordinateurs, les imprimantes de reçus et la sauvegarde des ventes.', 'Wolofobougou, Bamako', 'Bamako', 50000, 5, 6, ['Installation', 'Sauvegarde']],
            ['Remplacer la plomberie d’une douche', 'mamadou.client@barasira.test', 'Dépannage plomberie à domicile', 'mariam.plombiere@barasira.test', 'in_progress', 'Remplacer la tuyauterie, le mélangeur et vérifier l’évacuation de la douche.', 'Kayes N’Di, Kayes', 'Kayes', 80000, 2, 4, ['Plomberie', 'Évacuation']],
            ['Nettoyer une maison avant emménagement', 'aminata.client@barasira.test', 'Nettoyage de maison et bureau', 'awa.menage@barasira.test', 'cancelled', 'Nettoyage complet d’une maison de quatre pièces avant installation de la famille.', 'Kalaban Coura, Bamako', 'Bamako', 35000, 8, 8, ['Nettoyage', 'Désinfection']],
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
                'date_start' => now()->startOfDay()->addDays($startDay)->setHour(9),
                'date_end' => now()->startOfDay()->addDays($endDay)->setHour(12),
                'skills' => $skills,
                'questions' => ['Pouvez-vous confirmer votre disponibilité ?', 'Le matériel est-il inclus dans le tarif ?'],
            ]);
        }

        $this->command->info('Missions de démonstration créées avec succès.');
    }
}
