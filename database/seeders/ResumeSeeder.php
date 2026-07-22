<?php

namespace Database\Seeders;

use App\Models\Certification;
use App\Models\Education;
use App\Models\Experience;
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

        $profiles = [
            'ibrahim.electricien@barasira.test' => [
                'educations' => [
                    ['school_name' => 'Institut National de Formation Professionnelle', 'degree' => 'CAP', 'field' => 'Électricité bâtiment', 'start_year' => 2012, 'end_year' => 2014, 'city' => 'Bamako', 'country' => 'Mali'],
                ],
                'experiences' => [
                    ['company_name' => 'Électro Bâtiment Mali', 'position' => 'Électricien installateur', 'start_date' => '2015-01-01', 'end_date' => '2019-12-31', 'description' => 'Installation de tableaux électriques, câblage de logements et dépannage.'],
                    ['company_name' => 'Activité indépendante', 'position' => 'Électricien bâtiment', 'start_date' => '2020-01-01', 'end_date' => null, 'description' => 'Interventions auprès de particuliers et commerces à Bamako.'],
                ],
                'certifications' => [
                    ['name' => 'Habilitation électrique basse tension', 'issuer' => 'INFP Mali', 'issue_date' => '2021-06-15', 'expiration_date' => null, 'credential_url' => null],
                ],
            ],
            'mariam.plombiere@barasira.test' => [
                'educations' => [
                    ['school_name' => 'Centre de Formation Professionnelle de Sénou', 'degree' => 'Certificat professionnel', 'field' => 'Plomberie sanitaire', 'start_year' => 2016, 'end_year' => 2017, 'city' => 'Bamako', 'country' => 'Mali'],
                ],
                'experiences' => [
                    ['company_name' => 'Sanitaire Services', 'position' => 'Plombière', 'start_date' => '2017-09-01', 'end_date' => '2021-03-31', 'description' => 'Pose et réparation de sanitaires, recherche de fuites et entretien des canalisations.'],
                    ['company_name' => 'Activité indépendante', 'position' => 'Plombière à domicile', 'start_date' => '2021-04-01', 'end_date' => null, 'description' => 'Dépannage et installation sanitaire pour particuliers.'],
                ],
                'certifications' => [],
            ],
            'boubacar.informatique@barasira.test' => [
                'educations' => [
                    ['school_name' => 'Institut Universitaire de Gestion', 'degree' => 'DUT', 'field' => 'Informatique de gestion', 'start_year' => 2015, 'end_year' => 2017, 'city' => 'Bamako', 'country' => 'Mali'],
                ],
                'experiences' => [
                    ['company_name' => 'Bamako Digital Assistance', 'position' => 'Technicien support informatique', 'start_date' => '2018-02-01', 'end_date' => '2022-08-31', 'description' => 'Maintenance de postes, installation de réseaux locaux et assistance aux utilisateurs.'],
                ],
                'certifications' => [
                    ['name' => 'Google IT Support Professional', 'issuer' => 'Google', 'issue_date' => '2022-11-20', 'expiration_date' => null, 'credential_url' => 'https://www.coursera.org/professional-certificates/google-it-support'],
                    ['name' => 'Introduction to Cybersecurity', 'issuer' => 'Cisco Networking Academy', 'issue_date' => '2023-05-10', 'expiration_date' => null, 'credential_url' => 'https://www.netacad.com/courses/cybersecurity/introduction-cybersecurity'],
                ],
            ],
            'aissata.couture@barasira.test' => [
                'educations' => [
                    ['school_name' => 'Centre de Formation Féminine de Bamako', 'degree' => 'CAP', 'field' => 'Coupe et couture', 'start_year' => 2013, 'end_year' => 2015, 'city' => 'Bamako', 'country' => 'Mali'],
                ],
                'experiences' => [
                    ['company_name' => 'Atelier Aïssata Création', 'position' => 'Couturière et modéliste', 'start_date' => '2016-01-01', 'end_date' => null, 'description' => 'Création de tenues traditionnelles, retouches et accompagnement d’apprenties.'],
                ],
                'certifications' => [
                    ['name' => 'Perfectionnement en modélisme', 'issuer' => 'Maison de l’Artisanat de Bamako', 'issue_date' => '2019-08-30', 'expiration_date' => null, 'credential_url' => null],
                ],
            ],
            'youssouf.solaire@barasira.test' => [
                'educations' => [
                    ['school_name' => 'École Nationale d’Ingénieurs Abderhamane Baba Touré', 'degree' => 'Licence professionnelle', 'field' => 'Énergies renouvelables', 'start_year' => 2014, 'end_year' => 2017, 'city' => 'Bamako', 'country' => 'Mali'],
                ],
                'experiences' => [
                    ['company_name' => 'Mali Énergie Solaire', 'position' => 'Technicien photovoltaïque', 'start_date' => '2017-10-01', 'end_date' => '2022-12-31', 'description' => 'Dimensionnement, installation et maintenance de systèmes solaires autonomes.'],
                ],
                'certifications' => [
                    ['name' => 'Installation de systèmes photovoltaïques', 'issuer' => 'Agence des Énergies Renouvelables du Mali', 'issue_date' => '2020-02-14', 'expiration_date' => null, 'credential_url' => null],
                ],
            ],
            'nana.climatisation@barasira.test' => [
                'educations' => [
                    ['school_name' => 'Centre de Formation Professionnelle de Missabougou', 'degree' => 'BT', 'field' => 'Froid et climatisation', 'start_year' => 2015, 'end_year' => 2017, 'city' => 'Bamako', 'country' => 'Mali'],
                ],
                'experiences' => [
                    ['company_name' => 'Froid Confort Mali', 'position' => 'Technicienne frigoriste', 'start_date' => '2018-01-15', 'end_date' => '2023-06-30', 'description' => 'Installation et entretien de climatiseurs, réfrigérateurs et chambres froides.'],
                ],
                'certifications' => [
                    ['name' => 'Maintenance des équipements frigorifiques', 'issuer' => 'INFP Mali', 'issue_date' => '2021-09-18', 'expiration_date' => null, 'credential_url' => null],
                ],
            ],
        ];

        foreach ($profiles as $email => $data) {
            $provider = User::query()->where('email', $email)->first();
            $resume = $provider?->resume;

            if (! $resume) {
                continue;
            }

            foreach ($data['educations'] as $education) {
                Education::query()->updateOrCreate([
                    'resume_id' => $resume->id,
                    'school_name' => $education['school_name'],
                    'degree' => $education['degree'],
                ], $education);
            }

            foreach ($data['experiences'] as $experience) {
                Experience::query()->updateOrCreate([
                    'resume_id' => $resume->id,
                    'company_name' => $experience['company_name'],
                    'position' => $experience['position'],
                    'start_date' => $experience['start_date'],
                ], $experience);
            }

            foreach ($data['certifications'] as $certification) {
                Certification::query()->updateOrCreate([
                    'resume_id' => $resume->id,
                    'name' => $certification['name'],
                    'issuer' => $certification['issuer'],
                ], $certification);
            }
        }

        $this->command->info('Profils professionnels, diplômes, expériences et certifications créés avec succès.');
    }
}
