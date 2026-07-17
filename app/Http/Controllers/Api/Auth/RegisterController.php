<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserStoreRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    public function store(UserStoreRequest $request)
    {
        $googleProfile = $request->session()->get('google_registration');
        $isGoogleRegistration = is_array($googleProfile)
            && isset($googleProfile['google_id'], $googleProfile['email'])
            && hash_equals((string) $googleProfile['email'], (string) $request->email);

        // Création de l'utilisateur
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone' => $request->phone,
            'email' => $request->email ?? null,
            'role' => $request->role,
            'password' => Hash::make($isGoogleRegistration ? Str::random(40) : $request->password),
            'verified' => $isGoogleRegistration,
            'rating' => 0,
        ]);

        if ($isGoogleRegistration) {
            $user->forceFill([
                'google_id' => $googleProfile['google_id'],
                'avatar_url' => $googleProfile['avatar_url'] ?? null,
                'email_verified_at' => now(),
                'verified' => true,
            ])->save();
        }

        // Connexion automatique
        Auth::login($user);
        $request->session()->regenerate();
        $request->session()->forget(['google_registration', 'google_sso_intent']);

        // Email de vérification
        if (! $isGoogleRegistration) {
            try {
                $user->sendEmailVerificationNotification();
            } catch (\Throwable $e) {
                \Log::error('Email verification error: '.$e->getMessage());
            }
        }

        // Retour API JSON
        return response()->json([
            'success' => true,
            'message' => __($isGoogleRegistration ? 'auth.google_register_success' : 'auth.register_success'),
            'redirect' => $this->redirectPath($user),
            'user' => auth()->user(),
            'token' => $request->filled('device_name')
                ? $user->createToken($request->string('device_name')->toString())->plainTextToken
                : null,
        ], 201);
    }

    private function redirectPath($user)
    {
        return match ($user->role) {
            'admin', 'superadmin' => '/admin/dashboard',
            'prestataire' => '/provider/dashboard',
            'client' => '/dashboard',
        };
    }
}
