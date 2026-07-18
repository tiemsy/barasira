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

        $this->command->info('AdminSeeder exécuté avec succès.');
    }
}
