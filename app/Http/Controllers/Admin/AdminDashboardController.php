<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mission;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class AdminDashboardController extends Controller
{
    public function index(): Response
    {
        $roleCounts = User::query()
            ->selectRaw('role, COUNT(*) as total')
            ->groupBy('role')
            ->pluck('total', 'role');

        $missionCounts = Mission::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $stats = [
            'users' => $roleCounts->sum(),
            'providers' => (int) ($roleCounts['prestataire'] ?? 0),
            'clients' => (int) ($roleCounts['client'] ?? 0),
            'admins' => (int) ($roleCounts['admin'] ?? 0) + (int) ($roleCounts['superadmin'] ?? 0),
            'services' => Service::query()->count(),
            'missions' => $missionCounts->sum(),
            'pending_missions' => (int) ($missionCounts['pending'] ?? 0),
            'active_missions' => (int) ($missionCounts['in_progress'] ?? 0),
        ];

        $recentUsers = User::query()
            ->latest()
            ->limit(6)
            ->get(['id', 'first_name', 'last_name', 'email', 'role', 'avatar_url', 'created_at']);

        $recentServices = Service::query()
            ->with(['category:id,name', 'city:id,name', 'user:id,first_name,last_name'])
            ->latest()
            ->limit(6)
            ->get();

        $serviceStats = ServiceCategory::query()
            ->withCount('services')
            ->orderByDesc('services_count')
            ->limit(8)
            ->get(['id', 'name'])
            ->map(fn (ServiceCategory $category) => [
                'name' => $category->name,
                'count' => $category->services_count,
            ]);

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'recentUsers' => $recentUsers,
            'recentServices' => $recentServices,
            'userStats' => [
                'admin' => (int) ($roleCounts['admin'] ?? 0),
                'superadmin' => (int) ($roleCounts['superadmin'] ?? 0),
                'client' => (int) ($roleCounts['client'] ?? 0),
                'prestataire' => (int) ($roleCounts['prestataire'] ?? 0),
            ],
            'missionStats' => collect(['pending', 'in_progress', 'completed', 'cancelled'])
                ->mapWithKeys(fn (string $status) => [$status => (int) ($missionCounts[$status] ?? 0)]),
            'serviceStats' => $serviceStats,
        ]);
    }
}
