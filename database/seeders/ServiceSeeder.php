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
            ['ibrahim.electricien@barasira.test', 'Électricité', 'Bamako', 'commune-iv', 'Installation et dépannage électrique', 'Installation de tableaux, prises, éclairage et diagnostic de pannes pour maisons et bureaux.', 'electrical', 15000, 75000],
            ['mariam.plombiere@barasira.test', 'Plomberie', 'Bamako', 'commune-v', 'Dépannage plomberie à domicile', 'Réparation de fuites, débouchage, remplacement de robinets et installation d’équipements sanitaires.', 'plumbing', 7500, 45000],
            ['oumar.transport@barasira.test', 'Transport', 'Ségou', null, 'Transport de marchandises et déménagement', 'Transport sécurisé de colis, meubles et marchandises à Ségou, Bamako et dans les villes voisines.', 'transport', 10000, 120000],
            ['aissata.couture@barasira.test', 'Couture', 'Bamako', 'commune-ii', 'Couture traditionnelle et moderne', 'Confection sur mesure de boubous, robes, ensembles professionnels et retouches soignées.', 'tailoring', 5000, 50000],
            ['boubacar.informatique@barasira.test', 'Informatique', 'Bamako', 'commune-iii', 'Dépannage informatique et réseau', 'Installation de logiciels, nettoyage d’ordinateurs, récupération de données et configuration Wi-Fi.', 'computer', 10000, 60000],
            ['amadou.gardien@barasira.test', 'Gardiennage', 'Bamako', 'commune-v', 'Gardiennage de domicile et commerce', 'Surveillance de jour ou de nuit pour maisons, boutiques, bureaux et cérémonies.', 'shield', 10000, 90000],
            ['sekou.ouvrier@barasira.test', 'Main-d’œuvre et ouvriers', 'Bamako', 'commune-vi', 'Ouvrier polyvalent et manutention', 'Aide de chantier, chargement, déchargement, maçonnerie légère et peinture.', 'construction', 7500, 80000],
            ['awa.menage@barasira.test', 'Ménage', 'Bamako', 'commune-iv', 'Nettoyage de maison et bureau', 'Nettoyage régulier, grand ménage et remise en état après chantier ou déménagement.', 'cleaning', 7500, 50000],
            ['modibo.jardin@barasira.test', 'Agriculture et élevage', 'Ségou', null, 'Entretien maraîcher et travaux agricoles', 'Préparation de parcelles, arrosage, désherbage et entretien de petits périmètres maraîchers.', 'agriculture', 10000, 100000],
            ['kadidia.coiffure@barasira.test', 'Coiffure et beauté', 'Bamako', 'commune-i', 'Coiffure et tresses à domicile', 'Tresses, nattes, soins capillaires et coiffures pour mariages et cérémonies.', 'beauty', 5000, 40000],
            ['salif.mecanicien@barasira.test', 'Mécanique auto et moto', 'Sikasso', null, 'Dépannage mécanique auto et moto', 'Diagnostic, vidange, freinage et dépannage mécanique rapide sur place.', 'mechanic', 10000, 100000],
            ['fanta.traiteur@barasira.test', 'Restauration et traiteur', 'Bamako', 'commune-ii', 'Traiteur pour cérémonies et entreprises', 'Menus maliens, pauses-café et repas complets pour événements privés et professionnels.', 'food', 25000, 300000],
            ['youssouf.solaire@barasira.test', 'Énergie solaire', 'Kayes', null, 'Installation de kits solaires', 'Dimensionnement, pose de panneaux, batteries, régulateurs et maintenance des installations.', 'solar', 25000, 350000],
            ['nana.climatisation@barasira.test', 'Froid et climatisation', 'Bamako', 'commune-iii', 'Entretien climatisation et froid', 'Nettoyage, recharge, diagnostic et réparation de climatiseurs et réfrigérateurs.', 'cooling', 15000, 120000],
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
