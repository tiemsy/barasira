<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect(Request $request)
    {
        $request->session()->put(
            'google_sso_intent',
            $request->string('intent')->toString() === 'register' ? 'register' : 'login'
        );

        return Socialite::driver('google')->redirect();
    }

    public function callback(): RedirectResponse
    {
        $googleUser = Socialite::driver('google')->user();
        $email = $googleUser->getEmail();

        $user = $email ? User::query()->where('email', $email)->first() : null;

        if (! $user) {
            session()->put('google_registration', [
                'first_name' => $googleUser->user['given_name'] ?? $googleUser->getName() ?? '',
                'last_name' => $googleUser->user['family_name'] ?? '',
                'email' => $email ?? '',
                'google_id' => $googleUser->getId(),
                'avatar_url' => $googleUser->getAvatar(),
            ]);

            return redirect()->route('register')->with('error', __('auth.google_account_not_found'));
        }

        $user->forceFill([
            'google_id' => $googleUser->getId(),
            'avatar_url' => $user->avatar_url ?: $googleUser->getAvatar(),
            'email_verified_at' => $user->email_verified_at ?: now(),
            'verified' => true,
        ])->save();

        Auth::login($user, true);
        request()->session()->regenerate();
        request()->session()->forget(['google_registration', 'google_sso_intent']);

        $redirect = match ($user->role) {
            'superadmin', 'admin' => '/admin/dashboard',
            'prestataire' => '/provider/dashboard',
            'client' => '/dashboard',
            default => '/dashboard',
        };

        return redirect($redirect);
    }
}
