<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Service;
use App\Models\UserSkill;

class UserSkillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // On s’assure que des users et services existent
        $users = User::all();
        $services = Service::all();

        if ($users->isEmpty() || $services->isEmpty()) {
            $this->command->warn('Aucun user ou service trouvé : UserSkillSeeder ignoré.');
            return;
        }

        foreach ($users as $user) {
            // Chaque user reçoit entre 1 et 4 compétences
            $skillsCount = rand(1, 4);

            $services->random($skillsCount)->each(function ($service) use ($user) {
                UserSkill::factory()->create([
                    'user_id' => $user->id,
                    'service_id' => $service->id,
                ]);
            });
        }

        $this->command->info('UserSkillSeeder exécuté avec succès.');
    }
}
