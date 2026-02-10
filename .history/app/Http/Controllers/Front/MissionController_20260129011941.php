<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Mission;
use App\Repositories\Eloquent\MissionRepositoryEloquent;
use App\Repositories\Eloquent\UserRepositoryEloquent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class MissionController extends Controller
{
    protected $missionRepository;
    protected $userRepository;

    public function __construct(
        UserRepositoryEloquent $userRepository,
        MissionRepositoryEloquent $missionRepository)
    {
        $this->missionRepository = $missionRepository;
        $this->userRepository = $userRepository;
    }

    public function userMissions() {
        return Inertia::render('Missions/Index', [
            'missions' => $this->missionRepository->userMissions(Auth::user()),
            'prestataires' => $this->userRepository->missionProviders(Auth::user())
        ]);
    }

    public function show(Mission $mission)
    {
        return Inertia::render('Missions/Show', [
            // 'mission' => $mission->load(['client', 'service', 'applications', 'payments', 'reviews', 'messages'])
        ]);
    }
}
