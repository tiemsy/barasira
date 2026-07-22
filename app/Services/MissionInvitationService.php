<?php

namespace App\Services;

use App\Models\Mission;
use App\Models\MissionInvitation;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class MissionInvitationService
{
    /** @return array{invitation: MissionInvitation, token: string} */
    public function create(Mission $mission, User $client, User $provider): array
    {
        return DB::transaction(function () use ($mission, $client, $provider) {
            $mission = Mission::query()->whereKey($mission->id)->lockForUpdate()->firstOrFail();

            if ($client->role !== 'client' || $mission->client_id !== $client->id) {
                throw new AuthorizationException;
            }

            if ($provider->role !== 'prestataire') {
                throw ValidationException::withMessages(['provider_id' => __('missions.invitation.invalid_provider')]);
            }

            if ($mission->status !== 'pending' || $mission->prestataire_id !== null) {
                throw ValidationException::withMessages(['mission' => __('missions.unavailable')]);
            }

            MissionInvitation::query()
                ->where('mission_id', $mission->id)
                ->where('status', 'pending')
                ->update(['status' => 'cancelled', 'responded_at' => now()]);

            $token = Str::random(64);
            $invitation = MissionInvitation::create([
                'mission_id' => $mission->id,
                'client_id' => $client->id,
                'provider_id' => $provider->id,
                'token_hash' => hash('sha256', $token),
                'status' => 'pending',
                'expires_at' => now()->addHours(48),
            ]);

            return ['invitation' => $invitation, 'token' => $token];
        });
    }
}
