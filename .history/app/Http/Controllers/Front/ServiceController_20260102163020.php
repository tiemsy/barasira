<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Repositories\Eloquent\CityRepositoryEloquent;
use App\Repositories\Eloquent\ServiceCategoryRepositoryEloquent;
use App\Repositories\Eloquent\UserRepositoryEloquent;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ServiceController extends Controller
{
    protected $userRepository;
    protected $serviceCategory;
    protected $cityRepository;

    /**
     * Undocumented function
     *
     * @param UserRepositoryEloquent $userRepository
     * @param ServiceCategoryRepositoryEloquent $serviceCategory
     * @param CityRepositoryEloquent $cityRepository
     */
    public function __construct(
        UserRepositoryEloquent $userRepository,
        ServiceCategoryRepositoryEloquent $serviceCategory,
        CityRepositoryEloquent $cityRepository
    ) {
        $this->userRepository = $userRepository;
        $this->serviceCategory = $serviceCategory;
        $this->cityRepository = $cityRepository;
    }

    public function index()
    {
        return Inertia::render('Services/Index', [
            'users' => $this->userRepository->all(['services', 'missions']),
            'categories' => $this->serviceCategory->all(),
            'cities' => $this->cityRepository->all(),
        ]);
    }

    public function show(Service $service)
    {
        return Inertia::render('Services/Show', [
            'service' => $service->load(['user', 'category', 'city'])
        ]);
    }
}
