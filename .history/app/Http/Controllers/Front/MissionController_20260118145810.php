<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Mission;
use App\Repositories\Eloquent\MissionRepositoryEloquent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class MissionController extends Controller
{
    protected $missionRepository;

    public function __construct(MissionRepositoryEloquent $missionRepository)
    {
        $this->missionRepository = $missionRepository;
    }

    public function userMissions() {
        return Inertia::render('Missions/Index', [
            'missions' => $this->missionRepository->userMissions(Auth::user()),
        ]);
    }

    public function show(Mission $mission)
    {
        return Inertia::render('Missions/Show', [
            'mission' => $mission->load(['client', 'service', 'applications', 'payments', 'reviews', 'messages'])
        ]);
    }
}
