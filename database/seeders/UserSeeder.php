<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hourlyRates = [
            'ibrahim.electricien@BaraSira.test' => 10000,
            'mariam.plombiere@BaraSira.test' => 7500,
            'oumar.transport@BaraSira.test' => 12500,
            'aissata.couture@BaraSira.test' => 6000,
            'boubacar.informatique@BaraSira.test' => 10000,
            'amadou.gardien@BaraSira.test' => 5000,
            'sekou.ouvrier@BaraSira.test' => 6000,
            'awa.menage@BaraSira.test' => 5000,
            'modibo.jardin@BaraSira.test' => 6000,
            'kadidia.coiffure@BaraSira.test' => 7500,
            'salif.mecanicien@BaraSira.test' => 10000,
            'fanta.traiteur@BaraSira.test' => 15000,
            'youssouf.solaire@BaraSira.test' => 15000,
            'nana.climatisation@BaraSira.test' => 10000,
        ];

        $users = [
            ['Aminata', 'Traoré', 'aminata.client@BaraSira.test', '+223 76 10 20 30', 'client', 'Particulier à Bamako, je recherche des professionnels ponctuels pour l’entretien de mon logement.'],
            ['Moussa', 'Coulibaly', 'moussa.client@BaraSira.test', '+223 70 21 32 43', 'client', 'Responsable d’une petite entreprise à Ségou, je publie régulièrement des besoins de maintenance.'],
            ['Fatoumata', 'Diallo', 'fatoumata.client@BaraSira.test', '+223 66 32 43 54', 'client', 'Cliente à Bamako intéressée par les services de couture, de ménage et de dépannage.'],
            ['Adama', 'Maïga', 'adama.client@BaraSira.test', '+223 75 11 22 33', 'client', 'Commerçant à Bamako ayant régulièrement besoin de gardiennage, transport et manutention.'],
            ['Rokia', 'Sissoko', 'rokia.client@BaraSira.test', '+223 67 22 33 44', 'client', 'Responsable associative organisant des formations et événements à Sikasso.'],
            ['Mamadou', 'Dembélé', 'mamadou.client@BaraSira.test', '+223 71 33 44 55', 'client', 'Propriétaire à Kayes recherchant des artisans pour ses logements et son commerce.'],
            ['Assetou', 'Sangaré', 'assetou.client@BaraSira.test', '+223 63 44 55 66', 'client', 'Entrepreneure dans la restauration et le commerce de proximité à Bamako.'],
            ['Abdoulaye', 'Cissé', 'abdoulaye.client@BaraSira.test', '+223 77 55 66 77', 'client', 'Exploitant agricole à Ségou faisant appel à des ouvriers et techniciens locaux.'],
            ['Ibrahim', 'Konaté', 'ibrahim.electricien@BaraSira.test', '+223 74 40 50 60', 'prestataire', 'Électricien bâtiment avec huit années d’expérience en installation et dépannage à Bamako.'],
            ['Mariam', 'Diarra', 'mariam.plombiere@BaraSira.test', '+223 78 51 62 73', 'prestataire', 'Plombière spécialisée dans les réparations domestiques et les installations sanitaires.'],
            ['Oumar', 'Keïta', 'oumar.transport@BaraSira.test', '+223 72 62 73 84', 'prestataire', 'Transporteur professionnel pour les livraisons urbaines et interurbaines au Mali.'],
            ['Aïssata', 'Samaké', 'aissata.couture@BaraSira.test', '+223 65 73 84 95', 'prestataire', 'Couturière spécialisée dans les tenues traditionnelles et les créations sur mesure.'],
            ['Boubacar', 'Touré', 'boubacar.informatique@BaraSira.test', '+223 79 84 95 06', 'prestataire', 'Technicien informatique pour particuliers et petites entreprises.'],
            ['Amadou', 'Sow', 'amadou.gardien@BaraSira.test', '+223 73 14 25 36', 'prestataire', 'Agent de sécurité expérimenté pour domiciles, boutiques et événements.'],
            ['Sékou', 'Doumbia', 'sekou.ouvrier@BaraSira.test', '+223 68 25 36 47', 'prestataire', 'Ouvrier polyvalent en maçonnerie, peinture, manutention et travaux de chantier.'],
            ['Awa', 'Bagayoko', 'awa.menage@BaraSira.test', '+223 64 36 47 58', 'prestataire', 'Professionnelle du nettoyage de maisons, bureaux et espaces après travaux.'],
            ['Modibo', 'Camara', 'modibo.jardin@BaraSira.test', '+223 76 47 58 69', 'prestataire', 'Jardinier et ouvrier agricole spécialisé dans le maraîchage et l’entretien.'],
            ['Kadidia', 'Kané', 'kadidia.coiffure@BaraSira.test', '+223 69 58 69 70', 'prestataire', 'Coiffeuse spécialisée en tresses, soins capillaires et coiffures de cérémonie.'],
            ['Salif', 'Coulibaly', 'salif.mecanicien@BaraSira.test', '+223 74 69 70 81', 'prestataire', 'Mécanicien auto et moto avec service de dépannage à domicile.'],
            ['Fanta', 'Koné', 'fanta.traiteur@BaraSira.test', '+223 65 70 81 92', 'prestataire', 'Cuisinière et traiteur pour réunions, baptêmes, mariages et formations.'],
            ['Youssouf', 'Traoré', 'youssouf.solaire@BaraSira.test', '+223 72 81 92 03', 'prestataire', 'Technicien en énergie solaire, batteries et installations autonomes.'],
            ['Nana', 'Diarra', 'nana.climatisation@BaraSira.test', '+223 78 92 03 14', 'prestataire', 'Technicienne en froid, climatisation et entretien de réfrigérateurs.'],
        ];

        foreach ($users as [$firstName, $lastName, $email, $phone, $role, $bio]) {
            $avatarUrl = $this->createDemoAvatar($firstName, $lastName, $email, $role);

            User::query()->updateOrCreate(['email' => $email], [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'phone' => $phone,
                'password' => Hash::make('password'),
                'role' => $role,
                'bio' => $bio,
                'avatar_url' => $avatarUrl,
                'rating' => 0,
                'hourly_rate' => $hourlyRates[$email] ?? null,
                'verified' => true,
                'email_verified_at' => now(),
            ]);
        }

        $this->call(AdminSeeder::class);

        $this->command->info('UserSeeder exécuté avec succès.');
    }

    private function createDemoAvatar(string $firstName, string $lastName, string $email, string $role): string
    {
        $palette = $role === 'client'
            ? [['#DFF3E7', '#176B45'], ['#FFF0D6', '#925B12'], ['#E7EEFF', '#3156A3']]
            : [['#DDF0FF', '#17608C'], ['#EDE5FF', '#6842A8'], ['#FFE5EA', '#A43B53']];
        [$background, $foreground] = $palette[abs(crc32($email)) % count($palette)];
        $initials = mb_strtoupper(mb_substr($firstName, 0, 1).mb_substr($lastName, 0, 1));
        $safeInitials = htmlspecialchars($initials, ENT_XML1 | ENT_QUOTES, 'UTF-8');
        $svg = <<<SVG
        <svg xmlns="http://www.w3.org/2000/svg" width="256" height="256" viewBox="0 0 256 256">
          <rect width="256" height="256" rx="128" fill="{$background}"/>
          <circle cx="128" cy="102" r="46" fill="{$foreground}" opacity=".16"/>
          <path d="M50 230c8-48 38-74 78-74s70 26 78 74" fill="{$foreground}" opacity=".16"/>
          <text x="128" y="147" text-anchor="middle" font-family="Arial, sans-serif" font-size="68" font-weight="700" fill="{$foreground}">{$safeInitials}</text>
        </svg>
        SVG;
        $path = 'avatars/demo/'.Str::slug(Str::before($email, '@')).'.svg';

        Storage::disk('public')->put($path, $svg);

        return Storage::disk('public')->url($path);
    }
}
