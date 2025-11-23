<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Resume;
use App\Models\User;

class ResumeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->warn('Aucun utilisateur trouvé : ResumeSeeder ignoré.');
            return;
        }

        foreach ($users as $user) {
            // Chaque utilisateur peut avoir 1 à 2 CVs
            $resumeCount = rand(1, 2);

            Resume::factory()->count($resumeCount)->create([
                'user_id' => $user->id,
            ]);
        }

        $this->command->info('ResumeSeeder exécuté avec succès.');
    }
}
