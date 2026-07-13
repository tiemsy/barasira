<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Requests\MissionStoreRequest;
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
        MissionRepositoryEloquent $missionRepository
    ) {
        $this->missionRepository = $missionRepository;
        $this->userRepository = $userRepository;
    }

    public function userMissions(Request $request)
    {
        $user = $request->user();

        return Inertia::render('Missions/Index', [
            'missions' => $this->missionRepository->userMissions($user, $request->all()),
            'prestataires' => $this->userRepository->missionProviders(Auth::user())
        ]);
    }

    public function show(Mission $mission)
    {
        return Inertia::render('Missions/Show', [
            'mission' => $mission->load([
                'client',
                'prestataire',
                'service.category',
                'applications',
                'payments',
                'reviews',
                'messages',
            ])
        ]);
    }

    public function create(Request $request)
    {
        return Inertia::render('Missions/Create');
    }
}
