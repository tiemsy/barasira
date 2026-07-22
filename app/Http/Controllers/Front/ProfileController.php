<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Mission;
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

    private function profileData(Request $request): array
    {
        return $request->user()->only([
            'id',
            'first_name',
            'last_name',
            'email',
            'phone',
            'role',
            'bio',
            'avatar_url',
            'rating',
            'email_verified_at',
            'identity_verified_at',
        ]);
    }
}
