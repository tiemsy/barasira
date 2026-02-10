<?php

namespace App\Http\Controllers\Front;

use Inertia\Inertia;
use App\Http\Controllers\Controller;
use App\Repositories\Eloquent\CityRepositoryEloquent;
use App\Repositories\Eloquent\MissionRepositoryEloquent;
use App\Repositories\Eloquent\ServiceCategoryRepositoryEloquent;
use App\Repositories\Eloquent\UserRepositoryEloquent;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    protected $userRepository;
    protected $serviceCategory;
    protected $missionRepository;
    protected $cityRepository;

    /**
     * Undocumented function
     *
     * @param UserRepositoryEloquent $userRepository
     * @param ServiceCategoryRepositoryEloquent $serviceCategory
     * @param MissionRepositoryEloquent $missionRepository
     * @param CityRepositoryEloquent $cityRepository
     */
    public function __construct(
        UserRepositoryEloquent $userRepository,
        ServiceCategoryRepositoryEloquent $serviceCategory,
        MissionRepositoryEloquent $missionRepository,
        CityRepositoryEloquent $cityRepository
    ) {
        $this->userRepository = $userRepository;
        $this->serviceCategory = $serviceCategory;
        $this->cityRepository = $cityRepository;
        $this->missionRepository = $missionRepository;
    }

    public function index()
    {
        return Inertia::render('Home', [
            'users' => $this->userRepository->all(['services', 'missionsAsClient']),
            'randomCategories' => $this->serviceCategory->randomServiceCategories(),
            'categories' => $this->serviceCategory->all(),
            'cities' => $this->cityRepository->all(),
            'missions' => $this->missionRepository->homeMissions(),
        ]);
    }
}
