<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Service;
use App\Models\Mission;
use App\Models\ServiceCategory;
use Inertia\Inertia;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Stats globales
        $stats = [
            'users' => User::count(),
            'providers' => User::where('role', 'prestataire')->count(),
            'clients' => User::where('role', 'client')->count(),
            'admins' => User::where('role', 'admin')->count(),
            'services' => Service::count(),
            'missions' => Mission::count(),
        ];

        // Utilisateurs récents
        $recentUsers = User::latest()
            ->take(5)
            ->get(['id', 'first_name', 'last_name', 'email', 'role']);

        // Services récents
        $recentServices = Service::with(['category', 'city'])
            ->latest()
            ->take(5)
            ->get();

        // Stats utilisateurs par rôle
        $userStats = [
            'admin' => User::where('role', 'admin')->count(),
            'client' => User::where('role', 'client')->count(),
            'prestataire' => User::where('role', 'prestataire')->count(),
        ];

        // Services par catégorie
        $serviceCategories = ServiceCategory::pluck('name');
        $serviceCounts = ServiceCategory::withCount('services')
            ->pluck('services_count');

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'recentUsers' => $recentUsers,
            'recentServices' => $recentServices,
            'userStats' => $userStats,
            'serviceCategories' => $serviceCategories,
            'serviceCounts' => $serviceCounts,
        ]);
    }
}
