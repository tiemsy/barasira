<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserStoreRequest;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class RegisterController extends Controller
{
    public function store(UserStoreRequest $request)
    {
        // Création de l'utilisateur
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'phone'      => $request->phone,
            'email'      => $request->email ?? null,
            'role'       => $request->role,
            'password'   => Hash::make($request->password),
            'verified'   => false,
            'rating'     => 0,
        ]);

        // Connexion automatique
        Auth::login($user);

        // Email de vérification
        try {
            $user->sendEmailVerificationNotification();
        } catch (\Throwable $e) {
            \Log::error('Email verification error: ' . $e->getMessage());
        }

        // Retour API JSON
        return response()->json([
            'success' => true,
            'message' => __('auth.register_success'),
            'redirect' => $this->redirectPath($user),
            'user' => auth()->user(),
        ], 201);
    }

    private function redirectPath($user)
    {
        return match ($user->role) {
            'admin' => '/admin/dashboard',
            'prestataire' => '/provider/dashboard',
            'client' => '/dashboard',
        };
    }
}
