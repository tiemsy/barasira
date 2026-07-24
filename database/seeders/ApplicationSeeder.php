<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\Mission;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Seeder;

class ApplicationSeeder extends Seeder
{
    public function run(): void
    {
        Mission::query()
            ->whereNotNull('prestataire_id')
            ->get()
            ->values()
            ->each(function (Mission $mission, int $index): void {
                $usesHourlyRate = $index % 2 === 0;

                Application::query()->updateOrCreate(
                    ['mission_id' => $mission->id, 'worker_id' => $mission->prestataire_id],
                    [
                        'message' => 'Je suis disponible et qualifié pour réaliser cette mission dans les délais annoncés.',
                        'pricing_type' => $usesHourlyRate ? 'hourly' : 'global',
                        'hourly_rate' => $usesHourlyRate ? $mission->prestataire->hourly_rate : null,
                        'proposed_price' => $usesHourlyRate ? null : min(
                            (float) $mission->price,
                            (float) $mission->prestataire->hourly_rate * (float) $mission->initial_hours,
                        ),
                        'status' => in_array($mission->status, ['in_progress', 'completed'], true)
                            ? 'acceptee'
                            : 'refusee',
                    ],
                );
            });

        $pendingApplications = [
            'Confectionner trois tenues en bazin' => [
                'aissata.couture@barasira.test',
                'kadidia.coiffure@barasira.test',
                'awa.menage@barasira.test',
            ],
            'Installer deux ordinateurs de caisse' => [
                'boubacar.informatique@barasira.test',
                'ibrahim.electricien@barasira.test',
            ],
        ];

        foreach ($pendingApplications as $missionTitle => $providerEmails) {
            $mission = Mission::query()->where('title', $missionTitle)->firstOrFail();

            foreach ($providerEmails as $index => $providerEmail) {
                $provider = User::query()->where('email', $providerEmail)->firstOrFail();
                $usesHourlyRate = $index % 2 === 0;
                $application = Application::query()->updateOrCreate(
                    ['mission_id' => $mission->id, 'worker_id' => $provider->id],
                    [
                        'message' => 'Votre besoin correspond à mon expérience. Je peux intervenir sur le créneau proposé.',
                        'pricing_type' => $usesHourlyRate ? 'hourly' : 'global',
                        'hourly_rate' => $usesHourlyRate ? $provider->hourly_rate : null,
                        'proposed_price' => $usesHourlyRate ? null : min(
                            (float) $mission->price,
                            (float) $provider->hourly_rate * (float) $mission->initial_hours,
                        ),
                        'status' => 'en_attente',
                    ],
                );

                Notification::query()->updateOrCreate(
                    [
                        'user_id' => $mission->client_id,
                        'type' => 'mission_application',
                        'message' => "{$provider->first_name} {$provider->last_name} a postulé à votre mission « {$mission->title} ».",
                    ],
                    ['title' => 'Nouvelle candidature', 'read' => false],
                );
            }
        }

        $competingApplications = [
            'Installer un tableau électrique sécurisé' => [
                'boubacar.informatique@barasira.test',
                'youssouf.solaire@barasira.test',
            ],
            'Remplacer la plomberie d’une douche' => [
                'nana.climatisation@barasira.test',
            ],
            'Confectionner des uniformes de restaurant' => [
                'awa.menage@barasira.test',
            ],
        ];

        foreach ($competingApplications as $missionTitle => $providerEmails) {
            $mission = Mission::query()->where('title', $missionTitle)->firstOrFail();

            foreach ($providerEmails as $index => $providerEmail) {
                $provider = User::query()->where('email', $providerEmail)->firstOrFail();
                $usesHourlyRate = $index % 2 === 0;

                Application::query()->updateOrCreate(
                    ['mission_id' => $mission->id, 'worker_id' => $provider->id],
                    [
                        'message' => 'J’avais proposé mon intervention pour cette mission et transmis mes disponibilités au client.',
                        'pricing_type' => $usesHourlyRate ? 'hourly' : 'global',
                        'hourly_rate' => $usesHourlyRate ? $provider->hourly_rate : null,
                        'proposed_price' => $usesHourlyRate ? null : min(
                            (float) $mission->price,
                            (float) $provider->hourly_rate * (float) $mission->initial_hours,
                        ),
                        'status' => 'refusee',
                    ],
                );
            }
        }

        $this->command->info('Candidatures et notifications de démonstration créées avec succès.');
    }
}
