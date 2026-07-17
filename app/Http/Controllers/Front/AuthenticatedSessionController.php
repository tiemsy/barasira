<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'Email incorrect.',
                'password' => 'Mot de passe incorrect.',
                'general' => 'Une erreur est survenue. Veuillez réessayer.',
            ]);
        }

        // ✅ OK car route web
        $request->session()->regenerate();

        $user = Auth::user();

        // Email non vérifié → redirection
        if (! $user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        logger('USER AUTH', [
            'id' => auth()->id(),
            'role' => $user->role,
        ]);

        return response()->json([
            'redirect' => match ($user->role) {
                'admin', 'superadmin' => '/admin/dashboard',
                'prestataire' => '/provider/dashboard',
                default => '/dashboard',
            },
        ]);
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:20', 'unique:users,phone'],
            'email' => ['nullable', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:8'],
            'role' => ['required', Rule::in(['user', 'provider'])],
        ]);

        // Création de l'utilisateur
        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? null,
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
            'verified' => false,
            'rating' => 0,
        ]);

        // Connexion automatique
        $user = Auth::user();

        // Redirection Inertia
        return response()->json([
            'redirect' => match ($user->role) {
                'admin', 'superadmin' => '/admin/dashboard',
                'prestataire' => '/provider/dashboard',
                default => '/dashboard',
            },
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
