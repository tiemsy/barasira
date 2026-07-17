<?php

namespace Database\Seeders;

use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;

class ServiceCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Plomberie', 'description' => 'Installation sanitaire, recherche de fuite et dépannage.', 'icon' => 'fa-solid fa-water', 'is_active' => true],
            ['name' => 'Électricité', 'description' => 'Installation, mise aux normes et dépannage électrique.', 'icon' => 'fa-solid fa-bolt', 'is_active' => true],
            ['name' => 'Couture', 'description' => 'Confection, retouche et création de vêtements sur mesure.', 'icon' => 'fa-solid fa-scissors', 'is_active' => true],
            ['name' => 'Transport', 'description' => 'Livraison, déménagement et transport de marchandises.', 'icon' => 'fa-solid fa-truck', 'is_active' => true],
            ['name' => 'Jardinage', 'description' => 'Entretien des espaces verts et aménagement extérieur.', 'icon' => 'fa-solid fa-leaf', 'is_active' => true],
            ['name' => 'Ménage', 'description' => 'Nettoyage ponctuel ou régulier des logements et bureaux.', 'icon' => 'fa-solid fa-broom', 'is_active' => true],
            ['name' => 'Informatique', 'description' => 'Dépannage, installation et assistance numérique.', 'icon' => 'fa-solid fa-laptop', 'is_active' => true],
            ['name' => 'Bâtiment', 'description' => 'Maçonnerie, peinture, rénovation et petits travaux.', 'icon' => 'fa-solid fa-building', 'is_active' => true],
        ];

        foreach ($categories as $category) {
            ServiceCategory::updateOrCreate(
                ['name' => $category['name']],
                $category
            );
        }

        $this->command->info('Service categories seeded successfully.');
    }
}
