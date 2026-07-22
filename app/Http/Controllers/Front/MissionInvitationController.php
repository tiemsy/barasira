<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Mission;
use App\Models\MissionInvitation;
use App\Models\User;
use App\Notifications\MissionInvitationNotification;
use App\Services\MissionAssignmentService;
use App\Services\MissionInvitationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Inertia\Inertia;

class MissionInvitationController extends Controller
{
    public function store(Request $request, Mission $mission, MissionInvitationService $invitations)
    {
        $data = $request->validate(['provider_id' => ['required', 'integer', 'exists:users,id']]);
        $provider = User::query()->findOrFail($data['provider_id']);
        $result = $invitations->create($mission, $request->user(), $provider);
        $invitation = $result['invitation']->load(['mission:id,title', 'client:id,first_name,last_name']);
        $url = URL::temporarySignedRoute('front.mission-invitations.show', $invitation->expires_at, [
            'invitation' => $invitation->id,
            'token' => $result['token'],
        ]);

        try {
            $provider->notify(new MissionInvitationNotification($invitation, $url));
            $invitation->update(['email_sent_at' => now()]);
        } catch (\Throwable $exception) {
            report($exception);
        }

        return back()->with('success', __('missions.invitation.sent'));
    }

    public function show(Request $request, MissionInvitation $invitation)
    {
        abort_unless($request->user()->id === $invitation->provider_id, 403);

        return Inertia::render('Missions/Invitation', [
            'invitation' => $invitation->load(['mission.service.category', 'client:id,first_name,last_name']),
            'token' => (string) $request->query('token'),
        ]);
    }

    public function accept(Request $request, MissionInvitation $invitation, MissionAssignmentService $assignments)
    {
        $data = $request->validate(['token' => ['nullable', 'string', 'size:64']]);
        $mission = $assignments->acceptInvitation($invitation, $request->user(), $data['token'] ?? null);

        return redirect()->route('front.missions.show', ['mission' => $mission->slug])->with('success', __('missions.invitation.accepted'));
    }
}
