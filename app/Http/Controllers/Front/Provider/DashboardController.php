<?php

namespace App\Http\Controllers\Front\Provider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Statistiques ou missions spécifiques au prestataire
        $services = Service::latest()
            ->where('is_active', true)
            ->where('user_id', $user->id)
            ->take(5)
            ->with(['missions', 'city'])
            ->get();

        return Inertia::render('Provider/Dashboard', [
            'services' => $services,
        ]);
    }
}
