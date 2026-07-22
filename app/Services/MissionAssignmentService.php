<?php

namespace App\Services;

use App\Exceptions\MissionScheduleConflictException;
use App\Models\Mission;
use App\Models\MissionInvitation;
use App\Models\MissionUnassignment;
use App\Models\Service;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MissionAssignmentService
{
    public function claim(Mission $mission, User $provider): Mission
    {
        return DB::transaction(function () use ($mission, $provider) {
            User::query()->whereKey($provider->id)->lockForUpdate()->firstOrFail();
            $mission = Mission::query()->whereKey($mission->id)->lockForUpdate()->firstOrFail();

            if ($mission->status !== 'pending' || $mission->prestataire_id !== null) {
                throw ValidationException::withMessages([
                    'mission' => __('missions.unavailable'),
                ]);
            }

            if ($mission->invitations()->where('status', 'pending')->where('expires_at', '>', now())->exists()) {
                throw ValidationException::withMessages([
                    'mission' => __('missions.invitation.reserved'),
                ]);
            }

            $offersService = Service::query()
                ->whereKey($mission->service_id)
                ->activeForProvider($provider)
                ->exists();

            if (! $offersService) {
                throw new AuthorizationException(__('missions.service_mismatch'));
            }

            if ($this->hasScheduleConflict($mission, $provider)) {
                throw new MissionScheduleConflictException(__('missions.schedule_conflict'));
            }

            $mission->update([
                'prestataire_id' => $provider->id,
                'status' => 'in_progress',
            ]);

            return $mission->fresh(['client', 'prestataire', 'service.category']);
        });
    }

    public function acceptInvitation(MissionInvitation $invitation, User $provider, ?string $token = null): Mission
    {
        return DB::transaction(function () use ($invitation, $provider, $token) {
            User::query()->whereKey($provider->id)->lockForUpdate()->firstOrFail();
            $invitation = MissionInvitation::query()->whereKey($invitation->id)->lockForUpdate()->firstOrFail();
            $mission = Mission::query()->whereKey($invitation->mission_id)->lockForUpdate()->firstOrFail();

            if ($provider->role !== 'prestataire' || $invitation->provider_id !== $provider->id) {
                throw new AuthorizationException;
            }

            if ($token !== null && ! hash_equals($invitation->token_hash, hash('sha256', $token))) {
                throw new AuthorizationException(__('missions.invitation.invalid_link'));
            }

            if ($invitation->status !== 'pending' || $invitation->expires_at->isPast()) {
                throw ValidationException::withMessages(['invitation' => __('missions.invitation.expired')]);
            }

            if ($mission->status !== 'pending' || $mission->prestataire_id !== null) {
                throw ValidationException::withMessages(['mission' => __('missions.unavailable')]);
            }

            if ($this->hasScheduleConflict($mission, $provider)) {
                throw new MissionScheduleConflictException(__('missions.schedule_conflict'));
            }

            $mission->update(['prestataire_id' => $provider->id, 'status' => 'in_progress']);
            $invitation->update(['status' => 'accepted', 'responded_at' => now()]);
            MissionInvitation::query()
                ->where('mission_id', $mission->id)
                ->whereKeyNot($invitation->id)
                ->where('status', 'pending')
                ->update(['status' => 'cancelled', 'responded_at' => now()]);

            return $mission->fresh(['client', 'prestataire', 'service.category']);
        });
    }

    public function unassign(Mission $mission, User $client, string $reason, ?string $details = null): Mission
    {
        return DB::transaction(function () use ($mission, $client, $reason, $details) {
            $mission = Mission::query()->whereKey($mission->id)->lockForUpdate()->firstOrFail();

            if ($client->role !== 'client' || $mission->client_id !== $client->id) {
                throw new AuthorizationException;
            }

            if ($mission->status !== 'in_progress' || $mission->prestataire_id === null) {
                throw ValidationException::withMessages(['mission' => __('missions.unassignment.unavailable')]);
            }

            if ($mission->payments()->whereIn('status', ['en_attente', 'effectue'])->exists()) {
                throw ValidationException::withMessages(['mission' => __('missions.unassignment.payment_locked')]);
            }

            MissionUnassignment::create([
                'mission_id' => $mission->id,
                'client_id' => $client->id,
                'provider_id' => $mission->prestataire_id,
                'reason' => $reason,
                'details' => $details,
            ]);

            $mission->update(['prestataire_id' => null, 'status' => 'pending']);

            return $mission->fresh(['client', 'prestataire', 'service.category']);
        });
    }

    private function hasScheduleConflict(Mission $mission, User $provider): bool
    {
        $start = $mission->date_start;
        $end = $mission->date_end ?? $start;

        return Mission::query()
            ->where('prestataire_id', $provider->id)
            ->whereIn('status', ['pending', 'in_progress'])
            ->whereKeyNot($mission->id)
            ->where(function ($query) use ($start, $end) {
                $query->where('date_start', $start)
                    ->orWhere(function ($query) use ($start, $end) {
                        $query->where('date_start', '<', $end)
                            ->whereRaw('COALESCE(date_end, date_start) > ?', [$start]);
                    });
            })
            ->exists();
    }
}
