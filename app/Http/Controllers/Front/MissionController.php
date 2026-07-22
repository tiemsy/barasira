<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Mission;
use App\Models\User;
use App\Repositories\Eloquent\MissionRepositoryEloquent;
use App\Repositories\Eloquent\UserRepositoryEloquent;
use App\Services\MissionImageService;
use App\Services\MissionAssignmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class MissionController extends Controller
{
    protected $missionRepository;

    protected $userRepository;

    public function __construct(
        UserRepositoryEloquent $userRepository,
        MissionRepositoryEloquent $missionRepository,
        private readonly MissionImageService $missionImages,
    ) {
        $this->missionRepository = $missionRepository;
        $this->userRepository = $userRepository;
    }

    public function userMissions(Request $request)
    {
        $user = $request->user();

        return Inertia::render('Missions/Index', [
            'missions' => $this->missionRepository->userMissions($user, $request->all()),
            'prestataires' => $this->userRepository->missionProviders(Auth::user()),
        ]);
    }

    public function show(Request $request, Mission $mission)
    {
        $this->authorize('view', $mission);

        $canInvite = $request->user()->role === 'client'
            && $mission->client_id === $request->user()->id
            && $mission->status === 'pending'
            && $mission->prestataire_id === null;

        return Inertia::render('Missions/Show', [
            'mission' => $mission->load([
                'client',
                'prestataire',
                'service.category',
                'applications',
                'payments',
                'reviews',
                'images:id,mission_id,path,sort_order',
                'messages',
            ]),
            'providers' => $canInvite ? User::query()
                ->where('role', 'prestataire')
                ->select(['id', 'first_name', 'last_name', 'avatar_url', 'rating', 'bio'])
                ->orderByDesc('rating')
                ->orderBy('first_name')
                ->get() : [],
            'pendingInvitation' => $canInvite ? $mission->invitations()
                ->where('status', 'pending')
                ->where('expires_at', '>', now())
                ->with('provider:id,first_name,last_name')
                ->latest()
                ->first() : null,
        ]);
    }

    public function create(Request $request)
    {
        $this->authorize('create', Mission::class);

        return Inertia::render('Missions/Create');
    }

    public function edit(Mission $mission)
    {
        $this->authorize('update', $mission);
        abort_unless($mission->status === 'pending', 403);

        return Inertia::render('Missions/Edit', [
            'mission' => $mission,
        ]);
    }

    public function replaceImages(Request $request, Mission $mission)
    {
        abort_unless(
            $request->user()->role === 'client'
            && $mission->client_id === $request->user()->id
            && $mission->status === 'completed',
            403
        );

        $data = $request->validate([
            'images' => ['required', 'array', 'min:1', 'max:5'],
            'images.*' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ], [
            'images.required' => __('missions.images.required'),
            'images.array' => __('missions.images.required'),
            'images.min' => __('missions.images.count'),
            'images.max' => __('missions.images.count'),
            'images.*.required' => __('missions.images.required'),
            'images.*.image' => __('missions.images.invalid'),
            'images.*.mimes' => __('missions.images.format'),
            'images.*.max' => __('missions.images.size'),
        ]);

        $this->missionImages->replace($mission, $data['images']);

        return back()->with('success', __('missions.images_saved'));
    }

    public function unassignProvider(Request $request, Mission $mission, MissionAssignmentService $assignments)
    {
        $data = $request->validate([
            'reason' => ['required', Rule::in([
                'unavailable', 'poor_communication', 'lateness',
                'disagreement', 'client_change', 'other',
            ])],
            'details' => [
                Rule::requiredIf($request->input('reason') === 'other'),
                'nullable', 'string', 'min:10', 'max:1000',
            ],
        ], [
            'reason.required' => __('missions.unassignment.reason_required'),
            'reason.in' => __('missions.unassignment.reason_invalid'),
            'details.required' => __('missions.unassignment.details_required'),
            'details.min' => __('missions.unassignment.details_min'),
            'details.max' => __('missions.unassignment.details_max'),
        ]);

        $assignments->unassign(
            $mission,
            $request->user(),
            $data['reason'],
            $data['details'] ?? null,
        );

        return back()->with('success', __('missions.unassignment.success'));
    }
}
