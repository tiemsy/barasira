<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function store(Request $request)
    {
        // Validation
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        // Recherche user
        $user = User::where('email', $request->email)->first();

        // Vérification credentials
        if (! $user || ! Hash::check($request->password, $user->password)) {

            throw ValidationException::withMessages([
                'general' => 'Email ou mot de passe incorrect.',
            ]);
        }

        // Vérification email
        if (! $user->hasVerifiedEmail()) {

            return response()->json([
                'success' => false,
                'verified' => false,
                'message' => 'Veuillez vérifier votre adresse email.',
                'redirect' => '/email/verify',
            ], 403);
        }

        // Suppression anciens tokens
        $user->tokens()->delete();

        // Création token Sanctum
        $token = $user->createToken('mobile')->plainTextToken;

        logger('USER AUTH', [
            'id' => $user->id,
            'role' => $user->role,
        ]);

        // Redirection selon rôle
        $redirect = match ($user->role) {
            'admin' => '/admin/dashboard',
            'prestataire' => '/provider/dashboard',
            'client' => '/dashboard',
            default => '/',
        };

        return response()->json([
            'success' => true,
            'message' => 'Connexion réussie.',
            'token' => $token,
            'redirect' => $redirect,
            'user' => $user,
        ]);
    }
}
