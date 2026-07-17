<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Municipality;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\User;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            ['ibrahim.electricien@barasira.test', 'Électricité', 'Bamako', 'commune-iv', 'Installation et dépannage électrique', 'Installation de tableaux, prises, éclairage et diagnostic de pannes pour maisons et bureaux.', 'fa-solid fa-bolt', 15000, 75000],
            ['mariam.plombiere@barasira.test', 'Plomberie', 'Bamako', 'commune-v', 'Dépannage plomberie à domicile', 'Réparation de fuites, débouchage, remplacement de robinets et installation d’équipements sanitaires.', 'fa-solid fa-wrench', 7500, 45000],
            ['oumar.transport@barasira.test', 'Transport', 'Ségou', null, 'Transport de marchandises et déménagement', 'Transport sécurisé de colis, meubles et marchandises à Ségou, Bamako et dans les villes voisines.', 'fa-solid fa-truck', 10000, 120000],
            ['aissata.couture@barasira.test', 'Couture', 'Bamako', 'commune-ii', 'Couture traditionnelle et moderne', 'Confection sur mesure de boubous, robes, ensembles professionnels et retouches soignées.', 'fa-solid fa-scissors', 5000, 50000],
            ['boubacar.informatique@barasira.test', 'Informatique', 'Bamako', 'commune-iii', 'Dépannage informatique et réseau', 'Installation de logiciels, nettoyage d’ordinateurs, récupération de données et configuration Wi-Fi.', 'fa-solid fa-laptop', 10000, 60000],
        ];

        foreach ($services as [$email, $category, $city, $municipality, $name, $description, $icon, $minimum, $maximum]) {
            Service::query()->updateOrCreate(
                ['name' => $name],
                [
                    'user_id' => User::query()->where('email', $email)->value('id'),
                    'service_category_id' => ServiceCategory::query()->where('name', $category)->value('id'),
                    'city_id' => City::query()->where('name', $city)->value('id'),
                    'municipality_id' => $municipality ? Municipality::query()->where('slug', $municipality)->value('id') : null,
                    'description' => $description,
                    'icon' => $icon,
                    'price_min' => $minimum,
                    'price_max' => $maximum,
                    'is_active' => true,
                ]
            );
        }

        $this->command->info('Services de démonstration créés avec succès.');
    }
}
