<?php

namespace Database\Seeders;

use App\Models\Partner;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class PartnerSeeder extends Seeder
{
    public function run(): void
    {
        $adminId = User::query()->whereIn('role', ['superadmin', 'admin'])->value('id');
        $partners = [
            [
                'company_name' => 'Mali Énergie Solutions',
                'description' => 'Entreprise spécialisée dans les solutions énergétiques durables et l’accompagnement des professionnels locaux.',
                'logo' => 'mali-energie-solutions.svg',
                'website_url' => 'https://example.com/mali-energie-solutions',
                'company_email' => 'contact@mali-energie.test',
                'company_phone' => '+223 20 22 10 10',
                'address' => 'Hamdallaye ACI 2000, Bamako',
                'contact_name' => 'Aminata Coulibaly',
                'contact_position' => 'Responsable des partenariats',
                'contact_email' => 'aminata.coulibaly@mali-energie.test',
                'contact_phone' => '+223 76 10 20 30',
                'display_order' => 1,
            ],
            [
                'company_name' => 'Sugu Digital',
                'description' => 'Agence numérique qui soutient la transformation digitale des artisans, indépendants et petites entreprises maliennes.',
                'logo' => 'sugu-digital.svg',
                'website_url' => 'https://example.com/sugu-digital',
                'company_email' => 'bonjour@sugu-digital.test',
                'company_phone' => '+223 20 23 40 50',
                'address' => 'Badalabougou, Bamako',
                'contact_name' => 'Moussa Diarra',
                'contact_position' => 'Directeur commercial',
                'contact_email' => 'moussa.diarra@sugu-digital.test',
                'contact_phone' => '+223 70 40 50 60',
                'display_order' => 2,
            ],
            [
                'company_name' => 'Jigi Formation',
                'description' => 'Centre de formation professionnelle dédié au développement des compétences techniques et à l’employabilité des jeunes.',
                'logo' => 'jigi-formation.svg',
                'website_url' => null,
                'company_email' => 'contact@jigi-formation.test',
                'company_phone' => '+223 20 24 60 70',
                'address' => 'Sogoniko, Bamako',
                'contact_name' => 'Fatoumata Koné',
                'contact_position' => 'Coordinatrice des programmes',
                'contact_email' => 'fatoumata.kone@jigi-formation.test',
                'contact_phone' => '+223 75 60 70 80',
                'display_order' => 3,
            ],
            [
                'company_name' => 'Urgol Events Mali',
                'description' => 'Agence événementielle malienne spécialisée dans la conception, l’organisation et la coordination d’événements professionnels et privés.',
                'logo' => 'urgol-events-mali.svg',
                'website_url' => null,
                'company_email' => 'contact@urgol-events.test',
                'company_phone' => '+223 76 45 20 20',
                'address' => 'Bamako, Mali',
                'contact_name' => 'Responsable Urgol Events Mali',
                'contact_position' => 'Responsable des partenariats',
                'contact_email' => 'partenariats@urgol-events.test',
                'contact_phone' => '+223 76 45 20 20',
                'display_order' => 4,
            ],
            [
                'company_name' => 'Les Petits Stylos',
                'description' => 'Structure engagée dans l’éducation, l’éveil et l’accompagnement des enfants à travers des activités pédagogiques et créatives.',
                'logo' => 'les-petits-stylos.svg',
                'website_url' => null,
                'company_email' => 'contact@les-petits-stylos.test',
                'company_phone' => '+223 70 35 15 15',
                'address' => 'Bamako, Mali',
                'contact_name' => 'Responsable Les Petits Stylos',
                'contact_position' => 'Coordination',
                'contact_email' => 'coordination@les-petits-stylos.test',
                'contact_phone' => '+223 70 35 15 15',
                'display_order' => 5,
            ],
        ];

        foreach ($partners as $data) {
            $logo = $data['logo'];
            unset($data['logo']);
            $logoPath = "partners/{$logo}";
            Storage::disk('public')->put($logoPath, file_get_contents(database_path("seeders/assets/partners/{$logo}")));

            Partner::query()->updateOrCreate(
                ['company_name' => $data['company_name']],
                $data + [
                    'logo_path' => $logoPath,
                    'is_published' => true,
                    'created_by' => $adminId,
                    'updated_by' => $adminId,
                ]
            );
        }

        $this->command?->info(count($partners).' partenaires de démonstration créés avec succès.');
    }
}
