<?php

namespace App\Services;

use App\Models\Mission;
use App\Models\Review;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReviewService
{
    public function createForMission(User $client, array $data): Review
    {
        return DB::transaction(function () use ($client, $data) {
            $mission = Mission::query()->lockForUpdate()->findOrFail($data['mission_id']);

            if ($mission->client_id !== $client->id) {
                throw ValidationException::withMessages([
                    'mission_id' => __('Vous ne pouvez noter que vos propres missions.'),
                ]);
            }

            if ($mission->status !== 'completed' || $mission->prestataire_id === null) {
                throw ValidationException::withMessages([
                    'mission_id' => __('La mission doit être terminée avant de noter le prestataire.'),
                ]);
            }

            if (Review::query()->where('mission_id', $mission->id)->where('reviewer_id', $client->id)->exists()) {
                throw ValidationException::withMessages([
                    'mission_id' => __('Vous avez déjà donné votre avis pour cette mission.'),
                ]);
            }

            $review = Review::query()->create([
                'mission_id' => $mission->id,
                'reviewer_id' => $client->id,
                'reviewed_id' => $mission->prestataire_id,
                'rating' => $data['rating'],
                'comment' => $data['comment'] ?? null,
            ]);

            $this->refreshProviderRating($mission->prestataire_id);

            return $review->load('reviewer:id,first_name,last_name');
        });
    }

    public function revise(Review $review, User $client, array $data): Review
    {
        return DB::transaction(function () use ($review, $client, $data) {
            $review = Review::query()->lockForUpdate()->findOrFail($review->id);

            if ($review->reviewer_id !== $client->id) {
                throw ValidationException::withMessages([
                    'review' => __('Vous ne pouvez modifier que votre propre avis.'),
                ]);
            }

            if ($review->edit_count >= 1) {
                throw ValidationException::withMessages([
                    'review' => __('Cet avis a déjà été modifié et ne peut plus être changé.'),
                ]);
            }

            $review->update([
                'rating' => $data['rating'],
                'comment' => $data['comment'] ?? null,
                'edit_count' => 1,
                'revised_at' => now(),
            ]);

            $this->refreshProviderRating($review->reviewed_id);

            return $review->fresh('reviewer:id,first_name,last_name');
        });
    }

    private function refreshProviderRating(int $providerId): void
    {
        $average = Review::query()->where('reviewed_id', $providerId)->avg('rating') ?? 0;

        User::query()->whereKey($providerId)->update(['rating' => round((float) $average, 2)]);
    }
}
