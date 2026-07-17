<?php

namespace Database\Seeders;

use App\Models\Resume;
use App\Models\User;
use Illuminate\Database\Seeder;

class ResumeSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->where('role', 'prestataire')->get()->each(function (User $provider) {
            Resume::query()->updateOrCreate(
                ['user_id' => $provider->id],
                [
                    'title' => "Profil professionnel de {$provider->first_name} {$provider->last_name}",
                    'summary' => $provider->bio,
                    'visibility' => 'public',
                ]
            );
        });

        $this->command->info('Profils professionnels créés avec succès.');
    }
}
