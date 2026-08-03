<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Repositories\Eloquent\CityRepositoryEloquent;
use App\Repositories\Eloquent\ServiceCategoryRepositoryEloquent;
use App\Support\SeoMeta;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ServiceController extends Controller
{
    protected $serviceCategory;

    protected $cityRepository;

    /**
     * Undocumented function
     */
    public function __construct(
        ServiceCategoryRepositoryEloquent $serviceCategory,
        CityRepositoryEloquent $cityRepository
    ) {
        $this->serviceCategory = $serviceCategory;
        $this->cityRepository = $cityRepository;
    }

    public function index(Request $request)
    {
        return Inertia::render('Services/Index', [
            'seo' => SeoMeta::page(
                $request,
                'Services professionnels au Mali | BaraSira',
                'Recherchez des prestataires qualifiés par métier et par ville au Mali. Comparez les services et trouvez le professionnel adapté à votre besoin.'
            ),
            'categories' => $this->serviceCategory->all(),
            'cities' => $this->cityRepository->all(),
        ]);
    }

    public function show(Request $request, Service $service)
    {
        $service->load([
            'user' => fn ($query) => $query
                ->select(['id', 'first_name', 'last_name', 'avatar_url', 'bio', 'rating', 'hourly_rate', 'identity_verified_at'])
                ->with(['resume' => fn ($resume) => $resume
                    ->where('visibility', 'public')
                    ->with([
                        'educations' => fn ($query) => $query->latest('end_year'),
                        'experiences' => fn ($query) => $query->latest('start_date'),
                        'certifications' => fn ($query) => $query->latest('issue_date'),
                    ])])
                ->withCount([
                    'providerServices as active_services_count' => fn ($services) => $services->where('is_active', true),
                    'receivedReviews as reviews_count',
                    'missionsAsPrestataire as completed_missions_count' => fn ($missions) => $missions->where('status', 'completed'),
                ]),
            'category:id,name',
            'city:id,name',
            'municipality:id,name',
        ]);

        return Inertia::render('Services/Show', [
            'seo' => SeoMeta::service($request, $service),
            'service' => $service,
            'providerStats' => [
                'active_services' => (int) $service->user->active_services_count,
                'reviews' => (int) $service->user->reviews_count,
                'completed_missions' => (int) $service->user->completed_missions_count,
            ],
            'providerCredentials' => $request->user()?->role === 'client'
                ? $service->user->resume
                : null,
        ]);
    }
}
