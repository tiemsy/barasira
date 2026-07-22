<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Repositories\Eloquent\CityRepositoryEloquent;
use App\Repositories\Eloquent\ServiceCategoryRepositoryEloquent;
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

    public function index()
    {
        return Inertia::render('Services/Index', [
            'categories' => $this->serviceCategory->all(),
            'cities' => $this->cityRepository->all(),
        ]);
    }

    public function show(Request $request, Service $service)
    {
        $service->load([
            'user' => fn ($query) => $query
                ->select(['id', 'first_name', 'last_name', 'avatar_url', 'bio', 'rating', 'verified'])
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
                ]),
            'category:id,name',
            'city:id,name',
            'municipality:id,name',
        ]);

        return Inertia::render('Services/Show', [
            'service' => $service,
            'providerStats' => [
                'active_services' => (int) $service->user->active_services_count,
                'reviews' => (int) $service->user->reviews_count,
            ],
            'providerCredentials' => $request->user()?->role === 'client'
                ? $service->user->resume
                : null,
        ]);
    }
}
