<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReviewStoreRequest;
use App\Http\Requests\ReviewUpdateRequest;
use App\Models\Review;
use App\Services\ReviewService;
use Illuminate\Http\JsonResponse;

class ReviewController extends Controller
{
    public function __construct(private readonly ReviewService $reviewService) {}

    public function store(ReviewStoreRequest $request): JsonResponse
    {
        $review = $this->reviewService->createForMission($request->user(), $request->validated());

        return response()->json([
            'message' => __('Merci, votre avis a été publié.'),
            'review' => $review,
        ], 201);
    }

    public function update(ReviewUpdateRequest $request, Review $review): JsonResponse
    {
        $review = $this->reviewService->revise($review, $request->user(), $request->validated());

        return response()->json([
            'message' => __('Votre avis a été modifié. Il est maintenant définitif.'),
            'review' => $review,
        ]);
    }
}
