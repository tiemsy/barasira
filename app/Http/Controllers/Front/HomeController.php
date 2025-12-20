<?php

namespace App\Http\Controllers\Front;

use Inertia\Inertia;
use App\Http\Controllers\Controller;
use App\Repositories\Eloquent\UserRepositoryEloquent;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    protected $userRepository;

    /**
     * __construct function
     *
     * @param UserRepositoryEloquent $userRepository
     */
    public function __construct(UserRepositoryEloquent $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function index()
    {
        return Inertia::render('Dashboard', [
            'all' => [
                'users' => $this->userRepository->paginate(15, ['services']),
            ],
        ]);
    }
}
