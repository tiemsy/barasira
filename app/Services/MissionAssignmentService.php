<?php

namespace App\Services;

use App\Exceptions\MissionScheduleConflictException;
use App\Models\Mission;
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
                    'mission' => __('Cette mission n’est plus disponible.'),
                ]);
            }

            $offersService = Service::query()
                ->whereKey($mission->service_id)
                ->activeForProvider($provider)
                ->exists();

            if (! $offersService) {
                throw new AuthorizationException(__('Cette mission ne correspond pas à vos services actifs.'));
            }

            if ($this->hasScheduleConflict($mission, $provider)) {
                throw new MissionScheduleConflictException(__('Vous avez déjà une mission prévue sur ce créneau.'));
            }

            $mission->update([
                'prestataire_id' => $provider->id,
                'status' => 'in_progress',
            ]);

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
