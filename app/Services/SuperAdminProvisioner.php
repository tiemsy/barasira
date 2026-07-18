<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class SuperAdminProvisioner
{
    public function ensure(): User
    {
        $email = trim((string) config('superadmin.email'));
        $password = (string) config('superadmin.password');

        if ($password === '' && app()->environment('local', 'testing')) {
            $password = 'superadmin123';
        }

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException('SUPERADMIN_EMAIL doit contenir une adresse e-mail valide.');
        }

        if (strlen($password) < 12) {
            throw new RuntimeException('SUPERADMIN_PASSWORD est obligatoire et doit contenir au moins 12 caractères.');
        }

        $user = User::withTrashed()->firstOrNew(['email' => $email]);
        $user->forceFill([
            'first_name' => config('superadmin.first_name'),
            'last_name' => config('superadmin.last_name'),
            'phone' => config('superadmin.phone'),
            'password' => Hash::make($password),
            'role' => 'superadmin',
            'bio' => 'Superadministrateur de la plateforme Barasira.',
            'avatar_url' => null,
            'rating' => 0,
            'verified' => true,
            'email_verified_at' => now(),
            'deleted_at' => null,
        ])->save();

        return $user->fresh();
    }
}
