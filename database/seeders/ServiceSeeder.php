<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Municipality;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\User;
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
        $users = User::all();
        $cities = City::all();
        $municipalities = Municipality::all();

        if ($categories->isEmpty() || $users->isEmpty() || $cities->isEmpty() || $municipalities->isEmpty()) {
            $this->command->warn('Catégorie ou user ou ville ou commune non trouvée : ServiceSeeder ignoré.');
            return;
        }


        // Exemple manuel de service pour plus de réalisme
        Service::updateOrCreate([
            'name' => 'Installation Électrique'
        ], [
            'user_id' => User::where('role', 'prestataire')->inRandomOrder()->first()->id,
            'service_category_id' => $categories->where('name', 'Électricité')->first()->id ?? $categories->first()->id,
            'city_id' => $cities->where('name', 'Bamako')->first()->id,
            'municipality_id' => Municipality::inRandomOrder()->first()->id,
            'description' => 'Installation complète du réseau électrique pour maison ou bureau.',
            'icon' => 'fa-solid fa-bolt',
            'price_min' => 15000,
            'price_max' => 50000,
            'is_active' => true,
        ]);

        Service::create([
            'user_id' => User::where('role', 'prestataire')->inRandomOrder()->first()->id,
            'service_category_id' => $categories->where('name', 'Plomberie')->first()->id,
            'city_id' => $cities->where('name', 'Bamako')->first()->id,
            'municipality_id' => Municipality::inRandomOrder()->first()->id,
            'name' => 'Dépannage plomberie à domicile',
            'description' => 'Réparation de fuites, remplacement de robinets et installation sanitaire.',
            'price_min' => 5000,
            'price_max' => 15000,
            'is_active' => true,
        ]);

        Service::create([
            'user_id' => User::where('role', 'prestataire')->inRandomOrder()->first()->id,
            'service_category_id' => $categories->where('name', 'Transport')->first()->id,
            'city_id' => $cities->where('name', 'Ségou')->first()->id,
            'name' => 'Transport de marchandises locales',
            'description' => 'Transport sécurisé de colis et marchandises entre quartiers.',
            'price_min' => 7000,
            'price_max' => 20000,
            'is_active' => true,
        ]);

        Service::create([
            'user_id' => User::where('role', 'prestataire')->inRandomOrder()->first()->id,
            'service_category_id' => $categories->where('name', 'Couture')->first()->id,
            'city_id' => $cities->where('name', 'Kita')->first()->id,
            'name' => 'Couture traditionnelle et moderne',
            'description' => 'Confection de boubous, robes et habits sur mesure.',
            'price_min' => 4000,
            'price_max' => 12000,
            'is_active' => true,
        ]);

        // Générer 30 services aléatoires
        Service::factory()->count(30)->create();

        $this->command->info('ServiceSeeder exécuté avec succès.');
    }
}
