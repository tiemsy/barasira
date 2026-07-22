<?php

namespace App\Http\Controllers\Front\Client;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Mission;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $user = request()->user();
        $missions = Mission::query()->where('client_id', $user->id);
        $missionStats = (clone $missions)
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("SUM(CASE WHEN status IN ('pending', 'in_progress') THEN 1 ELSE 0 END) as active")
            ->selectRaw("SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed")
            ->first();

        return Inertia::render('Dashboard', [
            'stats' => [
                'total' => (int) $missionStats->total,
                'active' => (int) $missionStats->active,
                'completed' => (int) $missionStats->completed,
                'unread_messages' => Message::query()
                    ->where('receiver_id', $user->id)
                    ->where('read', false)
                    ->count(),
            ],
            'recentMissions' => (clone $missions)
                ->with(['prestataire:id,first_name,last_name', 'service:id,name'])
                ->latest()
                ->limit(5)
                ->get(['id', 'slug', 'client_id', 'prestataire_id', 'service_id', 'title', 'status', 'created_at']),
        ]);
    }
}
