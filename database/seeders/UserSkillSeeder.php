<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\UserSkill;
use Illuminate\Database\Seeder;

class UserSkillSeeder extends Seeder
{
    public function run(): void
    {
        Service::query()->get()->each(function (Service $service) {
            UserSkill::query()->updateOrCreate(
                ['user_id' => $service->user_id, 'service_id' => $service->id],
                [
                    'level' => 'expert',
                    'years_experience' => match ($service->name) {
                        'Installation et dépannage électrique' => 8,
                        'Dépannage plomberie à domicile' => 6,
                        'Transport de marchandises et déménagement' => 10,
                        'Couture traditionnelle et moderne' => 7,
                        default => 5,
                    },
                    'certificate' => 'Attestation professionnelle Barasira',
                    'certificate_file' => null,
                    'description' => "Compétence vérifiée pour le service « {$service->name} ».",
                    'verified' => true,
                ]
            );
        });

        $this->command->info('Compétences des prestataires créées avec succès.');
    }
}
