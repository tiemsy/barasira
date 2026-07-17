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
        return Inertia::render('Home', [
            'randomCategories' => $this->serviceCategory->randomServiceCategories(),
            'categories' => $this->serviceCategory->all(),
            'cities' => $this->cityRepository->all(),
            'missions' => $this->missionRepository->homeMissions(),
        ]);
    }
}
