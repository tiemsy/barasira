<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use App\Models\PartnerPromotion;
use App\Models\PlatformReview;
use App\Repositories\Eloquent\CityRepositoryEloquent;
use App\Repositories\Eloquent\MissionRepositoryEloquent;
use App\Repositories\Eloquent\ServiceCategoryRepositoryEloquent;
use App\Support\SeoMeta;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HomeController extends Controller
{
    protected $serviceCategory;

    protected $missionRepository;

    protected $cityRepository;

    /**
     * Undocumented function
     */
    public function __construct(
        ServiceCategoryRepositoryEloquent $serviceCategory,
        MissionRepositoryEloquent $missionRepository,
        CityRepositoryEloquent $cityRepository
    ) {
        $this->serviceCategory = $serviceCategory;
        $this->cityRepository = $cityRepository;
        $this->missionRepository = $missionRepository;
    }

    public function index(Request $request)
    {
        $categories = $this->serviceCategory->all();

        $featuredPartners = PartnerPromotion::query()
            ->active()
            ->whereHas('partner', fn ($query) => $query->where('is_published', true))
            ->with(['partner' => fn ($query) => $query->select(['id', 'company_name', 'description', 'logo_path', 'website_url'])])
            ->orderByDesc('paid_amount')
            ->orderBy('starts_at')
            ->limit(2)
            ->get()
            ->map(fn (PartnerPromotion $promotion) => [
                ...$promotion->partner->toArray(),
                'featured_until' => $promotion->ends_at->toIso8601String(),
            ]);

        $platformReviewQuery = PlatformReview::query()->where('is_published', true);
        $platformReviews = (clone $platformReviewQuery)
            ->with('user:id,first_name,last_name,avatar_url,role')
            ->latest()
            ->limit(3)
            ->get()
            ->map(fn (PlatformReview $review) => [
                'id' => $review->id,
                'rating' => $review->rating,
                'comment' => $review->comment,
                'created_at' => $review->created_at->toDateString(),
                'user' => $review->user,
            ]);

        return Inertia::render('Home', [
            'seo' => SeoMeta::page(
                $request,
                'Barasira — Trouvez un prestataire fiable au Mali',
                'Trouvez rapidement des prestataires qualifiés au Mali pour vos travaux, services à domicile et besoins professionnels.'
            ),
            'randomCategories' => $categories->shuffle()->take(4)->values(),
            'categories' => $categories,
            'cities' => $this->cityRepository->all(),
            'missions' => $this->missionRepository->homeMissions(),
            'partners' => Partner::query()->published()->whereNotIn('id', $featuredPartners->pluck('id'))->limit(8)->get(['id', 'company_name', 'description', 'logo_path', 'website_url']),
            'featuredPartners' => $featuredPartners,
            'platformReviews' => $platformReviews,
            'platformReviewStats' => [
                'average' => round((float) (clone $platformReviewQuery)->avg('rating'), 1),
                'count' => (clone $platformReviewQuery)->count(),
            ],
        ]);
    }
}
