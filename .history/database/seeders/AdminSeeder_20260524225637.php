<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ajouter un utilisateur super admin spécifique
        User::updateOrCreate(
            [
                'email' => 'admin@barasira.local',
            ],
            [
                'first_name' => 'Admin',
                'last_name' => 'Sira',
                'password' => Hash::make('admin123'),
                'role' => 'superadmin',
                'bio' => 'Administrateur principal de l’application Bara Sira.',
                'avatar_url' => null,
                'rating' => 5.0,
                'verified' => true,
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('AdminSeeder exécuté avec succès.');
    }
}
