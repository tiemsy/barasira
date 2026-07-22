<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            ['Aminata', 'Traoré', 'aminata.client@barasira.test', '+223 76 10 20 30', 'client', 'Particulier à Bamako, je recherche des professionnels ponctuels pour l’entretien de mon logement.'],
            ['Moussa', 'Coulibaly', 'moussa.client@barasira.test', '+223 70 21 32 43', 'client', 'Responsable d’une petite entreprise à Ségou, je publie régulièrement des besoins de maintenance.'],
            ['Fatoumata', 'Diallo', 'fatoumata.client@barasira.test', '+223 66 32 43 54', 'client', 'Cliente à Bamako intéressée par les services de couture, de ménage et de dépannage.'],
            ['Adama', 'Maïga', 'adama.client@barasira.test', '+223 75 11 22 33', 'client', 'Commerçant à Bamako ayant régulièrement besoin de gardiennage, transport et manutention.'],
            ['Rokia', 'Sissoko', 'rokia.client@barasira.test', '+223 67 22 33 44', 'client', 'Responsable associative organisant des formations et événements à Sikasso.'],
            ['Mamadou', 'Dembélé', 'mamadou.client@barasira.test', '+223 71 33 44 55', 'client', 'Propriétaire à Kayes recherchant des artisans pour ses logements et son commerce.'],
            ['Assetou', 'Sangaré', 'assetou.client@barasira.test', '+223 63 44 55 66', 'client', 'Entrepreneure dans la restauration et le commerce de proximité à Bamako.'],
            ['Abdoulaye', 'Cissé', 'abdoulaye.client@barasira.test', '+223 77 55 66 77', 'client', 'Exploitant agricole à Ségou faisant appel à des ouvriers et techniciens locaux.'],
            ['Ibrahim', 'Konaté', 'ibrahim.electricien@barasira.test', '+223 74 40 50 60', 'prestataire', 'Électricien bâtiment avec huit années d’expérience en installation et dépannage à Bamako.'],
            ['Mariam', 'Diarra', 'mariam.plombiere@barasira.test', '+223 78 51 62 73', 'prestataire', 'Plombière spécialisée dans les réparations domestiques et les installations sanitaires.'],
            ['Oumar', 'Keïta', 'oumar.transport@barasira.test', '+223 72 62 73 84', 'prestataire', 'Transporteur professionnel pour les livraisons urbaines et interurbaines au Mali.'],
            ['Aïssata', 'Samaké', 'aissata.couture@barasira.test', '+223 65 73 84 95', 'prestataire', 'Couturière spécialisée dans les tenues traditionnelles et les créations sur mesure.'],
            ['Boubacar', 'Touré', 'boubacar.informatique@barasira.test', '+223 79 84 95 06', 'prestataire', 'Technicien informatique pour particuliers et petites entreprises.'],
            ['Amadou', 'Sow', 'amadou.gardien@barasira.test', '+223 73 14 25 36', 'prestataire', 'Agent de sécurité expérimenté pour domiciles, boutiques et événements.'],
            ['Sékou', 'Doumbia', 'sekou.ouvrier@barasira.test', '+223 68 25 36 47', 'prestataire', 'Ouvrier polyvalent en maçonnerie, peinture, manutention et travaux de chantier.'],
            ['Awa', 'Bagayoko', 'awa.menage@barasira.test', '+223 64 36 47 58', 'prestataire', 'Professionnelle du nettoyage de maisons, bureaux et espaces après travaux.'],
            ['Modibo', 'Camara', 'modibo.jardin@barasira.test', '+223 76 47 58 69', 'prestataire', 'Jardinier et ouvrier agricole spécialisé dans le maraîchage et l’entretien.'],
            ['Kadidia', 'Kané', 'kadidia.coiffure@barasira.test', '+223 69 58 69 70', 'prestataire', 'Coiffeuse spécialisée en tresses, soins capillaires et coiffures de cérémonie.'],
            ['Salif', 'Coulibaly', 'salif.mecanicien@barasira.test', '+223 74 69 70 81', 'prestataire', 'Mécanicien auto et moto avec service de dépannage à domicile.'],
            ['Fanta', 'Koné', 'fanta.traiteur@barasira.test', '+223 65 70 81 92', 'prestataire', 'Cuisinière et traiteur pour réunions, baptêmes, mariages et formations.'],
            ['Youssouf', 'Traoré', 'youssouf.solaire@barasira.test', '+223 72 81 92 03', 'prestataire', 'Technicien en énergie solaire, batteries et installations autonomes.'],
            ['Nana', 'Diarra', 'nana.climatisation@barasira.test', '+223 78 92 03 14', 'prestataire', 'Technicienne en froid, climatisation et entretien de réfrigérateurs.'],
        ];

        foreach ($users as [$firstName, $lastName, $email, $phone, $role, $bio]) {
            User::query()->updateOrCreate(['email' => $email], [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'phone' => $phone,
                'password' => Hash::make('password'),
                'role' => $role,
                'bio' => $bio,
                'avatar_url' => null,
                'rating' => 0,
                'verified' => true,
                'email_verified_at' => now(),
            ]);
        }

        $this->call(AdminSeeder::class);

        $this->command->info('UserSeeder exécuté avec succès.');
    }
}
