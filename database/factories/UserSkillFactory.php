<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Service;
use App\Models\UserSkill;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserSkill>
 */
class UserSkillFactory extends Factory
{
    protected $model = UserSkill::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $levels = ['beginner', 'intermediate', 'expert'];

        return [
            'user_id' => User::factory(),
            'service_id' => Service::factory(),

            'level' => $this->faker->randomElement($levels),
            'years_experience' => $this->faker->numberBetween(0, 15),
            'certificate' => $this->faker->boolean(40)
                ? $this->faker->randomElement([
                    'Certificat Professionnel',
                    'Formation Courte',
                    'Attestation de Compétence',
                    'Brevet Technique'
                ])
                : null,
            'certificate_file' => null, // pour un vrai cas tu utiliseras un upload
            'description' => $this->faker->sentence(12),
            'verified' => $this->faker->boolean(30),
        ];
    }
}
