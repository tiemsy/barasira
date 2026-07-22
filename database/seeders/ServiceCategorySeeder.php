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
            ['name' => 'Plomberie', 'description' => 'Installation sanitaire, recherche de fuite et dépannage.', 'icon' => 'plumbing', 'is_active' => true],
            ['name' => 'Électricité', 'description' => 'Installation, mise aux normes et dépannage électrique.', 'icon' => 'electrical', 'is_active' => true],
            ['name' => 'Couture', 'description' => 'Confection, retouche et création de vêtements sur mesure.', 'icon' => 'tailoring', 'is_active' => true],
            ['name' => 'Transport', 'description' => 'Livraison, déménagement et transport de marchandises.', 'icon' => 'transport', 'is_active' => true],
            ['name' => 'Jardinage', 'description' => 'Entretien des espaces verts et aménagement extérieur.', 'icon' => 'gardening', 'is_active' => true],
            ['name' => 'Ménage', 'description' => 'Nettoyage ponctuel ou régulier des logements et bureaux.', 'icon' => 'cleaning', 'is_active' => true],
            ['name' => 'Informatique', 'description' => 'Dépannage, installation et assistance numérique.', 'icon' => 'computer', 'is_active' => true],
            ['name' => 'Bâtiment', 'description' => 'Maçonnerie, peinture, rénovation et petits travaux.', 'icon' => 'building', 'is_active' => true],
            ['name' => 'Gardiennage', 'description' => 'Surveillance de domiciles, commerces, bureaux et événements.', 'icon' => 'shield', 'is_active' => true],
            ['name' => 'Main-d’œuvre et ouvriers', 'description' => 'Ouvriers polyvalents, manutentionnaires et aide sur les chantiers.', 'icon' => 'construction', 'is_active' => true],
            ['name' => 'Coiffure et beauté', 'description' => 'Coiffure, tresses, soins esthétiques et préparation cérémonielle.', 'icon' => 'beauty', 'is_active' => true],
            ['name' => 'Restauration et traiteur', 'description' => 'Cuisine locale, restauration collective et prestations pour cérémonies.', 'icon' => 'food', 'is_active' => true],
            ['name' => 'Mécanique auto et moto', 'description' => 'Entretien, diagnostic et réparation de véhicules et motos.', 'icon' => 'mechanic', 'is_active' => true],
            ['name' => 'Énergie solaire', 'description' => 'Installation et entretien de panneaux, batteries et équipements solaires.', 'icon' => 'solar', 'is_active' => true],
            ['name' => 'Agriculture et élevage', 'description' => 'Travaux agricoles, maraîchage, soins et entretien des animaux.', 'icon' => 'agriculture', 'is_active' => true],
            ['name' => 'Froid et climatisation', 'description' => 'Installation, entretien et dépannage de climatiseurs et réfrigérateurs.', 'icon' => 'cooling', 'is_active' => true],
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
