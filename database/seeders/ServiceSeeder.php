<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = ServiceCategory::all();

        if ($categories->isEmpty()) {
            $this->command->warn('Aucune catégorie trouvée : ServiceSeeder ignoré.');
            return;
        }

        // Générer 30 services aléatoires
        Service::factory()->count(30)->create();

        // Exemple manuel de service pour plus de réalisme
        Service::updateOrCreate([
            'name' => 'Installation Électrique'
        ], [
            'category_id' => $categories->where('name', 'Électricité')->first()->id ?? $categories->first()->id,
            'description' => 'Installation complète du réseau électrique pour maison ou bureau.',
            'icon' => 'fa-solid fa-bolt',
            'price_min' => 15000,
            'price_max' => 50000,
            'is_active' => true,
        ]);

        $this->command->info('ServiceSeeder exécuté avec succès.');
    }
}
