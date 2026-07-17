<?php

namespace Database\Factories;

use App\Models\Service;
use App\Models\User;
use App\Models\UserSkill;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserSkillFactory extends Factory
{
    protected $model = UserSkill::class;

    public function configure(): static
    {
        return $this->afterCreating(fn (UserSkill $skill) => $skill->update([
            'user_id' => $skill->service->user_id,
        ]));
    }

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->provider(),
            'service_id' => Service::factory(),
            'level' => 'expert',
            'years_experience' => 5,
            'certificate' => 'Attestation professionnelle',
            'certificate_file' => null,
            'description' => 'Compétence pratique confirmée par plusieurs années d’interventions professionnelles.',
            'verified' => true,
        ];
    }
}
