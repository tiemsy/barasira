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
            ['Ibrahim', 'Konaté', 'ibrahim.electricien@barasira.test', '+223 74 40 50 60', 'prestataire', 'Électricien bâtiment avec huit années d’expérience en installation et dépannage à Bamako.'],
            ['Mariam', 'Diarra', 'mariam.plombiere@barasira.test', '+223 78 51 62 73', 'prestataire', 'Plombière spécialisée dans les réparations domestiques et les installations sanitaires.'],
            ['Oumar', 'Keïta', 'oumar.transport@barasira.test', '+223 72 62 73 84', 'prestataire', 'Transporteur professionnel pour les livraisons urbaines et interurbaines au Mali.'],
            ['Aïssata', 'Samaké', 'aissata.couture@barasira.test', '+223 65 73 84 95', 'prestataire', 'Couturière spécialisée dans les tenues traditionnelles et les créations sur mesure.'],
            ['Boubacar', 'Touré', 'boubacar.informatique@barasira.test', '+223 79 84 95 06', 'prestataire', 'Technicien informatique pour particuliers et petites entreprises.'],
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
