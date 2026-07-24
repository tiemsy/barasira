<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Mission;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    public function show(Request $request): Response
    {
        $user = $request->user();

        return Inertia::render('Profile/Show', [
            'profile' => $this->profileData($request),
            'resume' => $user->role === 'prestataire'
                ? $user->resume()->with([
                    'educations' => fn ($query) => $query->latest('end_year'),
                    'experiences' => fn ($query) => $query->latest('start_date'),
                    'certifications' => fn ($query) => $query->latest('issue_date'),
                ])->first()
                : null,
            'documents' => $user->role === 'prestataire'
                ? $user->documents()->latest('uploaded_at')->get([
                    'id', 'document_type', 'label', 'original_name', 'mime_type', 'file_size',
                    'status', 'review_comment', 'reviewed_at', 'uploaded_at',
                ])
                : [],
            'completedMissions' => $user->role === 'prestataire'
                ? Mission::query()
                    ->where('prestataire_id', $user->id)
                    ->where('status', 'completed')
                    ->whereHas('payments', fn ($query) => $query->where('status', 'effectue'))
                    ->whereHas('images')
                    ->with([
                        'service:id,name',
                        'images:id,mission_id,path,sort_order',
                    ])
                    ->latest('date_end')
                    ->limit(12)
                    ->get(['id', 'service_id', 'title', 'description', 'city', 'date_end'])
                : [],
        ]);
    }

    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'profile' => $this->profileData($request),
        ]);
    }

    public function clientProfile(Request $request, User $client): Response
    {
        abort_unless(
            $client->role === 'client'
            && in_array($request->user()->role, ['prestataire', 'admin', 'superadmin'], true),
            403,
        );

        $sourceMission = null;
        if ($request->user()->role === 'prestataire') {
            $missionSlug = $request->string('mission')->toString();
            abort_if($missionSlug === '', 403);

            $sourceMission = Mission::query()
                ->where('slug', $missionSlug)
                ->where('client_id', $client->id)
                ->firstOrFail();
            $this->authorize('view', $sourceMission);
        } elseif ($request->filled('mission')) {
            $sourceMission = Mission::query()
                ->where('slug', $request->string('mission')->toString())
                ->where('client_id', $client->id)
                ->firstOrFail();
        }

        $client->loadCount('missionsAsClient');

        return Inertia::render('Profile/ClientShow', [
            'client' => [
                ...$client->only(['id', 'slug', 'first_name', 'last_name', 'avatar_url', 'bio', 'created_at']),
                'missions_created_count' => $client->missions_as_client_count,
            ],
            'comments' => $client->clientComments()
                ->with('commenter:id,first_name,last_name,avatar_url,identity_verified_at')
                ->latest()
                ->get(),
            'myComment' => $request->user()->role === 'prestataire'
                ? $client->clientComments()->where('commenter_id', $request->user()->id)->first()
                : null,
            'backMissionUrl' => $sourceMission
                ? route('front.missions.show', ['mission' => $sourceMission->slug])
                : route('admin.users.index'),
        ]);
    }

    public function storeClientComment(Request $request, User $client)
    {
        abort_unless($request->user()->role === 'prestataire' && $client->role === 'client', 403);

        $data = $request->validate([
            'comment' => ['required', 'string', 'min:10', 'max:2000'],
        ]);

        $client->clientComments()->updateOrCreate(
            ['commenter_id' => $request->user()->id],
            ['comment' => $data['comment']],
        );

        return back()->with('success', __('messages.client_comment_saved'));
    }

    private function profileData(Request $request): array
    {
        $user = $request->user();
        $profile = $user->only([
            'id',
            'first_name',
            'last_name',
            'email',
            'phone',
            'role',
            'bio',
            'avatar_url',
            'rating',
            'hourly_rate',
            'email_verified_at',
            'identity_verified_at',
        ]);

        if ($user->role === 'client') {
            $profile['missions_created_count'] = $user->missionsAsClient()->count();
        } elseif ($user->role === 'prestataire') {
            $profile['missions_completed_count'] = $user->missionsAsPrestataire()
                ->where('status', 'completed')
                ->count();
        }

        return $profile;
    }
}
