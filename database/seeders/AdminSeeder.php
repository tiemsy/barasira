<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            [
                'email' => 'admin@barasira.com',
            ],
            [
                'first_name' => 'Admin',
                'last_name' => 'Sira',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'bio' => 'Administrateur principal de l’application Bara Sira.',
                'avatar_url' => null,
                'rating' => 5.0,
                'verified' => true,
                'email_verified_at' => now(),
            ]
        );

        $superAdminPassword = env('SUPERADMIN_PASSWORD', app()->environment('production') ? null : 'superadmin123');

        if ($superAdminPassword) {
            User::updateOrCreate(
                ['email' => env('SUPERADMIN_EMAIL', 'superadmin@barasira.com')],
                [
                    'first_name' => 'Super',
                    'last_name' => 'Admin',
                    'phone' => env('SUPERADMIN_PHONE', '+223 70 00 00 00'),
                    'password' => Hash::make($superAdminPassword),
                    'role' => 'superadmin',
                    'bio' => 'Superadministrateur de la plateforme Barasira.',
                    'avatar_url' => null,
                    'rating' => 0,
                    'verified' => true,
                    'email_verified_at' => now(),
                ]
            );
        } else {
            $this->command->warn('SUPERADMIN_PASSWORD absent : compte superadmin non créé en production.');
        }

        $this->command->info('AdminSeeder exécuté avec succès.');
    }
}
