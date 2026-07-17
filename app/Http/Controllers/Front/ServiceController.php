<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Service;
use App\Repositories\Eloquent\CityRepositoryEloquent;
use App\Repositories\Eloquent\ServiceCategoryRepositoryEloquent;
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

    public function show(Service $service)
    {
        $service->load(['user', 'category', 'city', 'municipality']);

        return Inertia::render('Services/Show', [
            'service' => $service,
            'providerStats' => [
                'active_services' => Service::query()
                    ->where('user_id', $service->user_id)
                    ->where('is_active', true)
                    ->count(),
                'reviews' => Review::query()
                    ->where('reviewed_id', $service->user_id)
                    ->count(),
            ],
        ]);
    }
}
