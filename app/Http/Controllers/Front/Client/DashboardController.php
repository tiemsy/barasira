<?php

namespace App\Http\Controllers\Front\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        // Statistiques ou services spécifiques au client
        $services = Service::latest()->take(5)->get();

        return Inertia::render('Dashboard', [
            'services' => $services,
        ]);
    }
}
