<?php

namespace App\Policies;

use App\Models\Mission;
use App\Models\User;

class MissionPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['client', 'prestataire'], true) || $user->isAdmin();
    }

    public function view(User $user, Mission $mission): bool
    {
        return $user->isAdmin()
            || (int) $mission->client_id === (int) $user->id
            || ($mission->prestataire_id !== null
                && (int) $mission->prestataire_id === (int) $user->id)
            || ($user->role === 'prestataire' && $mission->applications()
                ->where('worker_id', $user->id)
                ->exists())
            || ($user->role === 'prestataire' && $mission->invitations()
                ->where('provider_id', $user->id)
                ->where('status', 'pending')
                ->where('expires_at', '>', now())
                ->exists())
            || ($user->role === 'prestataire'
                && $mission->status === 'pending'
                && $mission->prestataire_id === null);
    }

    public function apply(User $user, Mission $mission): bool
    {
        return $user->role === 'prestataire'
            && $mission->status === 'pending'
            && $mission->prestataire_id === null;
    }

    public function create(User $user): bool
    {
        return $user->role === 'client' || $user->isAdmin();
    }

    public function update(User $user, Mission $mission): bool
    {
        return $user->isAdmin()
            || $mission->client_id === $user->id
            || $mission->prestataire_id === $user->id;
    }

    public function delete(User $user, Mission $mission): bool
    {
        return $user->isAdmin()
            || ($mission->client_id === $user->id && $mission->status === 'pending');
    }
}
