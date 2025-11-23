<?php

namespace Database\Factories;

use App\Models\Review;
use App\Models\User;
use App\Models\Mission;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Review>
 */
class ReviewFactory extends Factory
{
    protected $model = Review::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $reviewers = User::all();
        $reviewedUsers = User::all();

        // S'assurer qu'il y a au moins deux utilisateurs
        if ($reviewers->count() < 2) {
            return [];
        }

        // Choisir deux utilisateurs distincts
        $reviewer = $reviewers->random();
        do {
            $reviewed = $reviewedUsers->random();
        } while ($reviewed->id === $reviewer->id);

        return [
            'mission_id' => Mission::inRandomOrder()->first()->id ?? Mission::factory(),
            'reviewer_id' => $reviewer->id,
            'reviewed_id' => $reviewed->id,
            'rating' => $this->faker->randomFloat(1, 1, 5),
            'comment' => $this->faker->paragraph(2),
        ];
    }
}
