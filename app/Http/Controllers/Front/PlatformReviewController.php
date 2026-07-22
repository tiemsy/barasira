<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Requests\PlatformReviewRequest;
use App\Models\PlatformReview;
use App\Support\SeoMeta;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PlatformReviewController extends Controller
{
    public function index(Request $request): Response
    {
        $reviews = PlatformReview::query()
            ->where('is_published', true)
            ->with('user:id,first_name,last_name,avatar_url,role')
            ->latest()
            ->paginate(12)
            ->through(fn (PlatformReview $review) => [
                'id' => $review->id,
                'rating' => $review->rating,
                'comment' => $review->comment,
                'created_at' => $review->created_at->toDateString(),
                'user' => $review->user,
            ]);

        return Inertia::render('PlatformReviews/Index', [
            'reviews' => $reviews,
            'myReview' => $request->user()
                ? PlatformReview::query()->whereBelongsTo($request->user())->first(['id', 'rating', 'comment'])
                : null,
            'averageRating' => round((float) PlatformReview::query()->where('is_published', true)->avg('rating'), 1),
            'seo' => SeoMeta::page(
                $request,
                'Avis sur Barasira | Expériences des utilisateurs',
                'Découvrez les avis des clients et prestataires qui utilisent Barasira au Mali.'
            ),
        ]);
    }

    public function store(PlatformReviewRequest $request): RedirectResponse
    {
        PlatformReview::query()->updateOrCreate(
            ['user_id' => $request->user()->id],
            [...$request->validated(), 'is_published' => true]
        );

        return back()->with('success', __('messages.platform_review_saved'));
    }
}
