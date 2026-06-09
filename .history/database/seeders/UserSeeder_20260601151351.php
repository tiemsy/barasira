<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Générer 20 utilisateurs aléatoires
        User::factory()->count(20)->create();

        // Ajouter un utilisateur super admin spécifique
        User::factory()->create([
            'first_name' => 'Admin',
            'last_name' => 'Sira',
            'email' => 'admin@barasira.local',
            'password' => bcrypt('admin123'),
            'role' => 'admin',
            'bio' => 'Administrateur principal de l’application Bara Sira.',
            'avatar_url' => null,
            'rating' => 5.0,
            'verified' => true,
        ]);

        $this->command->info('UserSeeder exécuté avec succès.');
    }
}
