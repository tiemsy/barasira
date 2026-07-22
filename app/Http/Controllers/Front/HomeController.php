<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Repositories\Eloquent\CityRepositoryEloquent;
use App\Repositories\Eloquent\MissionRepositoryEloquent;
use App\Repositories\Eloquent\ServiceCategoryRepositoryEloquent;
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

    public function index()
    {
        $categories = $this->serviceCategory->all();

        return Inertia::render('Home', [
            'randomCategories' => $categories->shuffle()->take(4)->values(),
            'categories' => $categories,
            'cities' => $this->cityRepository->all(),
            'missions' => $this->missionRepository->homeMissions(),
        ]);
    }
}
