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

        return Inertia::render('Dashboard', [
            'stats' => [
                'total' => (clone $missions)->count(),
                'active' => (clone $missions)->whereIn('status', ['pending', 'in_progress'])->count(),
                'completed' => (clone $missions)->where('status', 'completed')->count(),
                'unread_messages' => Message::query()
                    ->where('receiver_id', $user->id)
                    ->where('read', false)
                    ->count(),
            ],
            'recentMissions' => (clone $missions)
                ->with(['prestataire:id,first_name,last_name', 'service:id,name'])
                ->latest()
                ->limit(5)
                ->get(),
        ]);
    }
}
