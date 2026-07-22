<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function store(Request $request)
    {
        // Validation
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
            'device_name' => 'nullable|string|max:100',
        ]);

        $credentials = $request->only('email', 'password');

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                // 'email' => 'Email incorrect.',
                // 'password' => 'Mot de passe incorrect.',
                'general' => 'Email ou mot de passe incorrect. Veuillez réessayer.',
            ]);
        }

        $user = $request->user();

        if (! $user->hasVerifiedEmail()) {
            // Auth::logout();
            return response()->json([
                'redirect' => '/email/verify',
            ]);

            // throw ValidationException::withMessages([
            //     // 'verified' => 'Veuillez vérifier votre adresse email avant de vous connecter.',
            //     'redirect' => '/email/verify'
            // ], 403);
        }

        // $request->session()->regenerate();
        logger('USER AUTH', [
            'id' => $user->id,
            'session' => session()->all(),
        ]);

        // Email non vérifié → redirection
        // if (!$user->hasVerifiedEmail()) {
        //     return redirect()->route('verification.notice');
        // }

        logger('USER AUTH', [
            'id' => auth()->id(),
            'role' => $user->role,
        ]);

        $redirect = match ($user->role) {
            'superadmin', 'admin' => '/admin/dashboard',
            'prestataire' => '/provider/dashboard',
            'client' => '/dashboard',
            default => '/dashboard',
        };

        // dd($redirect);

        return response()->json([
            'success' => true,
            'message' => __('messages.login_success'),
            'redirect' => $redirect,
            'user' => $user,
            // Les clients natifs utilisent un jeton Bearer; le site web conserve sa session cookie.
            'token' => $request->filled('device_name')
                ? $user->createToken($request->string('device_name')->toString())->plainTextToken
                : null,
        ], 200);

        // Retour API JSON
        // return response()->json([
        //     'success' => true,
        //     'message' => 'Connexion réussie',
        //     // 'token' => $token,
        //     'redirect' => '/dashboard',
        //     'user' => $user,
        // ], 200);
    }
}
