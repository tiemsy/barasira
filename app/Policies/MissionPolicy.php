<?php

namespace App\Policies;

use App\Models\Mission;
use App\Models\Service;
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
            || $mission->client_id === $user->id
            || $mission->prestataire_id === $user->id
            || ($user->role === 'prestataire' && $mission->invitations()
                ->where('provider_id', $user->id)
                ->where('status', 'pending')
                ->where('expires_at', '>', now())
                ->exists())
            || ($user->role === 'prestataire'
                && $mission->status === 'pending'
                && $mission->prestataire_id === null
                && $this->offersMissionService($user, $mission));
    }

    public function claim(User $user, Mission $mission): bool
    {
        return $user->role === 'prestataire'
            && $mission->status === 'pending'
            && $mission->prestataire_id === null
            && $this->offersMissionService($user, $mission);
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

    private function offersMissionService(User $user, Mission $mission): bool
    {
        return Service::query()
            ->whereKey($mission->service_id)
            ->activeForProvider($user)
            ->exists();
    }
}
