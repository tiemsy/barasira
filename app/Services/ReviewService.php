<?php

namespace App\Services;

use App\Models\Mission;
use App\Models\Review;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReviewService
{
    public function createForMission(User $reviewer, array $data): Review
    {
        return DB::transaction(function () use ($reviewer, $data) {
            $mission = Mission::query()->lockForUpdate()->findOrFail($data['mission_id']);

            if (! $reviewer->isAdmin() && $mission->client_id !== $reviewer->id) {
                throw ValidationException::withMessages([
                    'mission_id' => __('messages.review_own_missions_only'),
                ]);
            }

            if ($mission->status !== 'completed' || $mission->prestataire_id === null) {
                throw ValidationException::withMessages([
                    'mission_id' => __('messages.review_mission_not_completed'),
                ]);
            }

            if (Review::query()->where('mission_id', $mission->id)->where('reviewer_id', $reviewer->id)->exists()) {
                throw ValidationException::withMessages([
                    'mission_id' => __('messages.review_already_exists'),
                ]);
            }

            $review = Review::query()->create([
                'mission_id' => $mission->id,
                'reviewer_id' => $reviewer->id,
                'reviewed_id' => $mission->prestataire_id,
                'rating' => $data['rating'],
                'comment' => $data['comment'] ?? null,
            ]);

            $this->refreshProviderRating($mission->prestataire_id);

            return $review->load('reviewer:id,first_name,last_name');
        });
    }

    public function revise(Review $review, User $reviewer, array $data): Review
    {
        return DB::transaction(function () use ($review, $reviewer, $data) {
            $review = Review::query()->lockForUpdate()->findOrFail($review->id);

            if ($review->reviewer_id !== $reviewer->id) {
                throw ValidationException::withMessages([
                    'review' => __('messages.review_own_only'),
                ]);
            }

            if ($review->edit_count >= 1) {
                throw ValidationException::withMessages([
                    'review' => __('messages.review_already_revised'),
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
