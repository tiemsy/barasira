<?php

namespace Database\Factories;

use App\Models\Mission;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReviewFactory extends Factory
{
    protected $model = Review::class;

    public function configure(): static
    {
        return $this->afterCreating(fn (Review $review) => $review->update([
            'reviewer_id' => $review->mission->client_id,
            'reviewed_id' => $review->mission->prestataire_id,
        ]));
    }

    public function definition(): array
    {
        return [
            'mission_id' => Mission::factory()->completed(),
            'reviewer_id' => User::factory()->client(),
            'reviewed_id' => User::factory()->provider(),
            'rating' => 5,
            'comment' => 'Prestataire ponctuel, communication claire et travail conforme à la demande.',
        ];
    }
}
