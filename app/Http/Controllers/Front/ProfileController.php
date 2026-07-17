<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    public function show(Request $request): Response
    {
        return Inertia::render('Profile/Show', [
            'profile' => $this->profileData($request),
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
        ]);
    }
}
