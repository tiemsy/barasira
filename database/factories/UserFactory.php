<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    protected static ?string $password;

    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
            'phone' => '+223 7'.$this->faker->numerify('# ## ## ##'),
            'role' => 'client',
            'bio' => 'Utilisateur Barasira à la recherche de services fiables au Mali.',
            'avatar_url' => null,
            'rating' => 0,
            'verified' => true,
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn () => ['email_verified_at' => null, 'verified' => false]);
    }

    public function client(): static
    {
        return $this->state(fn () => ['role' => 'client']);
    }

    public function provider(): static
    {
        return $this->state(fn () => ['role' => 'prestataire', 'bio' => 'Prestataire expérimenté proposant des interventions professionnelles et ponctuelles.']);
    }

    public function admin(): static
    {
        return $this->state(fn () => ['role' => 'admin']);
    }

    public function superAdmin(): static
    {
        return $this->state(fn () => ['role' => 'superadmin']);
    }
}
