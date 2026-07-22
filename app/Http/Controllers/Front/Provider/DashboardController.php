<?php

namespace App\Http\Controllers\Front\Provider;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Message;
use App\Models\Mission;
use App\Models\MissionInvitation;
use App\Models\Service;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $user = request()->user();
        $serviceIds = Service::query()->activeForProvider($user)->select('id');
        $assignedMissions = Mission::query()->where('prestataire_id', $user->id);

        return Inertia::render('Provider/Dashboard', [
            'stats' => [
                'services' => Service::query()->where('user_id', $user->id)->count(),
                'active_missions' => (clone $assignedMissions)->where('status', 'in_progress')->count(),
                'applications' => Application::query()->where('worker_id', $user->id)->count(),
                'rating' => round((float) $user->rating, 1),
                'unread_messages' => Message::query()->where('receiver_id', $user->id)->where('read', false)->count(),
            ],
            'assignedMissions' => (clone $assignedMissions)
                ->with(['client:id,first_name,last_name', 'service:id,name'])
                ->latest()
                ->limit(5)
                ->get(['id', 'slug', 'client_id', 'prestataire_id', 'service_id', 'title', 'status', 'created_at']),
            'missionInvitations' => MissionInvitation::query()
                ->where('provider_id', $user->id)
                ->where('status', 'pending')
                ->where('expires_at', '>', now())
                ->with([
                    'client:id,first_name,last_name',
                    'mission:id,slug,client_id,service_id,title,city,address,price,date_start,date_end,status',
                    'mission.service:id,name',
                ])
                ->latest()
                ->get(),
            'availableMissions' => Mission::query()
                ->whereNull('prestataire_id')
                ->where('status', 'pending')
                ->whereDoesntHave('invitations', fn ($query) => $query
                    ->where('status', 'pending')
                    ->where('expires_at', '>', now()))
                ->whereIn('service_id', $serviceIds)
                ->with(['client:id,first_name,last_name', 'service:id,name'])
                ->latest()
                ->limit(5)
                ->get(['id', 'slug', 'client_id', 'prestataire_id', 'service_id', 'title', 'city', 'address', 'price', 'status', 'created_at']),
        ]);
    }
}
