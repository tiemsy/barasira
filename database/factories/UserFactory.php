<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $firstName = $this->faker->firstName();
        $lastName = $this->faker->lastName();

        return [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $this->faker->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'), // mot de passe par défaut : password
            'phone' => $this->faker->phoneNumber(),
            'role' => $this->faker->randomElement(['client', 'provider', 'admin']),
            'bio' => $this->faker->paragraph(2),
            'avatar_url' => $this->faker->imageUrl(200, 200, 'people'),
            'rating' => $this->faker->randomFloat(2, 1, 5), // note entre 1 et 5
            'verified' => $this->faker->boolean(70), // 70% chance que l'utilisateur soit vérifié
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
