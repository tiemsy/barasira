<?php

namespace App\Http\Controllers\Front\Provider;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Message;
use App\Models\Mission;
use App\Models\Review;
use App\Models\Service;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $user = request()->user();
        $serviceIds = Service::query()->activeForProvider($user)->pluck('id');
        $assignedMissions = Mission::query()->where('prestataire_id', $user->id);

        return Inertia::render('Provider/Dashboard', [
            'stats' => [
                'services' => Service::query()->where('user_id', $user->id)->count(),
                'active_missions' => (clone $assignedMissions)->where('status', 'in_progress')->count(),
                'applications' => Application::query()->where('worker_id', $user->id)->count(),
                'rating' => round((float) Review::query()->where('reviewed_id', $user->id)->avg('rating'), 1),
                'unread_messages' => Message::query()->where('receiver_id', $user->id)->where('read', false)->count(),
            ],
            'assignedMissions' => (clone $assignedMissions)
                ->with(['client:id,first_name,last_name', 'service:id,name'])
                ->latest()
                ->limit(5)
                ->get(),
            'availableMissions' => Mission::query()
                ->whereNull('prestataire_id')
                ->where('status', 'pending')
                ->whereIn('service_id', $serviceIds)
                ->with(['client:id,first_name,last_name', 'service:id,name'])
                ->latest()
                ->limit(5)
                ->get(),
        ]);
    }
}
