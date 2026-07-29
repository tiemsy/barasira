<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Mission;
use App\Models\Notification;
use App\Models\User;
use App\Notifications\ApplicationAcceptedNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;

class MissionApplicationController extends Controller
{
    public function store(Request $request, Mission $mission): JsonResponse
    {
        $this->authorize('apply', $mission);

        $data = $request->validate([
            'message' => ['nullable', 'string', 'max:2000'],
            'pricing_type' => ['required', 'in:hourly,global'],
            'hourly_rate' => ['nullable', 'required_if:pricing_type,hourly', 'numeric', 'min:1', 'max:99999999.99'],
            'proposed_price' => ['nullable', 'required_if:pricing_type,global', 'numeric', 'min:1', 'max:99999999.99'],
        ]);

        $application = DB::transaction(function () use ($request, $mission, $data) {
            $mission = Mission::query()->whereKey($mission->id)->lockForUpdate()->firstOrFail();

            if ($mission->status !== 'pending' || $mission->prestataire_id !== null) {
                throw ValidationException::withMessages(['mission' => __('missions.unavailable')]);
            }

            if ($mission->invitations()->where('status', 'pending')->where('expires_at', '>', now())->exists()) {
                throw ValidationException::withMessages(['mission' => __('missions.invitation.reserved')]);
            }

            if ($this->hasApplicationScheduleConflict($mission, $request->user()->id)) {
                throw ValidationException::withMessages([
                    'mission' => __('missions.application.schedule_conflict'),
                ]);
            }

            $application = Application::query()->firstOrCreate(
                ['mission_id' => $mission->id, 'worker_id' => $request->user()->id],
                [
                    'message' => $data['message'] ?? null,
                    'pricing_type' => $data['pricing_type'],
                    'hourly_rate' => $data['pricing_type'] === 'hourly' ? $data['hourly_rate'] : null,
                    'proposed_price' => $data['pricing_type'] === 'global' ? $data['proposed_price'] : null,
                    'status' => 'en_attente',
                ],
            );

            if (! $application->wasRecentlyCreated) {
                throw ValidationException::withMessages(['mission' => __('missions.application.already_applied')]);
            }

            $recipient = User::query()->findOrFail($mission->client_id);
            Notification::query()->create([
                'user_id' => $mission->client_id,
                'type' => 'mission_application',
                'title' => $this->translatedFor($recipient, 'missions.application.notification_title'),
                'message' => $this->translatedFor($recipient, 'missions.application.notification_message', [
                    'provider' => trim($request->user()->first_name.' '.$request->user()->last_name),
                    'mission' => $mission->title,
                ]),
                'read' => false,
            ]);

            return $application;
        });

        return response()->json([
            'success' => true,
            'message' => __('missions.application.submitted'),
            'data' => $application,
        ], 201);
    }

    private function hasApplicationScheduleConflict(Mission $mission, int $providerId): bool
    {
        $start = $mission->date_start;
        $end = $mission->date_end ?? $start;

        return Mission::query()
            ->whereKeyNot($mission->id)
            ->whereIn('status', ['pending', 'in_progress'])
            ->where(function ($query) use ($providerId) {
                $query->where('prestataire_id', $providerId)
                    ->orWhereHas('applications', fn ($query) => $query
                        ->where('worker_id', $providerId)
                        ->whereIn('status', ['en_attente', 'acceptee']));
            })
            ->where(function ($query) use ($start, $end) {
                $query->where('date_start', $start)
                    ->orWhere(function ($query) use ($start, $end) {
                        $query->where('date_start', '<', $end)
                            ->whereRaw('COALESCE(date_end, date_start) > ?', [$start]);
                    });
            })
            ->exists();
    }

    public function accept(Request $request, Mission $mission, Application $application): JsonResponse
    {
        abort_unless(
            $request->user()->role === 'client'
            && $mission->client_id === $request->user()->id
            && $application->mission_id === $mission->id,
            403,
        );

        [$mission, $application] = DB::transaction(function () use ($mission, $application) {
            $mission = Mission::query()->whereKey($mission->id)->lockForUpdate()->firstOrFail();
            $application = Application::query()->whereKey($application->id)->lockForUpdate()->firstOrFail();

            if ($mission->status !== 'pending' || $mission->prestataire_id !== null || $application->status !== 'en_attente') {
                throw ValidationException::withMessages(['mission' => __('missions.unavailable')]);
            }

            $mission->update([
                'prestataire_id' => $application->worker_id,
                'status' => 'in_progress',
            ]);
            $application->update(['status' => 'acceptee']);
            $mission->applications()
                ->whereKeyNot($application->id)
                ->where('status', 'en_attente')
                ->update(['status' => 'refusee']);
            $mission->invitations()
                ->where('status', 'pending')
                ->update(['status' => 'cancelled', 'responded_at' => now()]);

            $recipient = User::query()->findOrFail($application->worker_id);
            Notification::query()->create([
                'user_id' => $application->worker_id,
                'type' => 'application_accepted',
                'title' => $this->translatedFor($recipient, 'missions.application.accepted_notification_title'),
                'message' => $this->translatedFor($recipient, 'missions.application.accepted_notification_message', [
                    'mission' => $mission->title,
                ]),
                'read' => false,
            ]);

            return [$mission, $application];
        });

        $application->load(['mission:id,slug,title', 'user:id,first_name,last_name,email,phone,locale']);
        $url = URL::route('front.missions.show', ['mission' => $mission->slug]);

        try {
            $application->user->notify(new ApplicationAcceptedNotification($application, $url));
        } catch (\Throwable $exception) {
            report($exception);
        }

        return response()->json([
            'success' => true,
            'message' => __('missions.application.accepted'),
            'data' => $mission->fresh([
                'client',
                'prestataire',
                'service.category',
                'applications.user:id,first_name,last_name,avatar_url,rating,hourly_rate,bio',
            ]),
        ]);
    }

    private function translatedFor(User $recipient, string $key, array $replace = []): string
    {
        return __($key, $replace, $recipient->preferredLocale());
    }
}
