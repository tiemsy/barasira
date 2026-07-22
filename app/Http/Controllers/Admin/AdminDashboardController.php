<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Mission;
use App\Models\Payment;
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
            'pending_documents' => Document::query()
                ->where('status', 'en_attente')
                ->whereHas('user', fn ($query) => $query->where('role', 'prestataire'))
                ->count(),
        ];

        $recentUsers = User::query()
            ->latest()
            ->limit(6)
            ->get(['id', 'first_name', 'last_name', 'email', 'role', 'avatar_url', 'created_at']);

        $recentServices = Service::query()
            ->with(['category:id,name', 'city:id,name', 'user:id,first_name,last_name'])
            ->latest()
            ->limit(6)
            ->get(['id', 'user_id', 'service_category_id', 'city_id', 'name', 'icon', 'is_active', 'created_at']);

        $serviceStats = ServiceCategory::query()
            ->withCount('services')
            ->orderByDesc('services_count')
            ->limit(8)
            ->get(['id', 'name'])
            ->map(fn (ServiceCategory $category) => [
                'name' => $category->name,
                'count' => $category->services_count,
            ]);

        $paymentStatuses = Payment::query()
            ->selectRaw('status, COUNT(*) as total, COALESCE(SUM(amount), 0) as amount')
            ->selectRaw('COALESCE(SUM(platform_fee), 0) as platform_fee')
            ->selectRaw('COALESCE(SUM(provider_amount), 0) as provider_amount')
            ->selectRaw('COALESCE(AVG(amount), 0) as average_amount')
            ->groupBy('status')
            ->get()
            ->keyBy('status');
        $paymentTotal = $paymentStatuses->sum('total');
        $completedPayments = $paymentStatuses->get('effectue');

        $transactionStats = [
            'total' => (int) $paymentTotal,
            'gross_volume' => (float) ($completedPayments?->amount ?? 0),
            'platform_revenue' => (float) ($completedPayments?->platform_fee ?? 0),
            'provider_payouts' => (float) ($completedPayments?->provider_amount ?? 0),
            'pending_amount' => (float) ($paymentStatuses->get('en_attente')?->amount ?? 0),
            'refunded_amount' => (float) ($paymentStatuses->get('rembourse')?->amount ?? 0),
            'average_amount' => (float) ($completedPayments?->average_amount ?? 0),
            'success_rate' => $paymentTotal > 0
                ? round(((int) ($completedPayments?->total ?? 0) / $paymentTotal) * 100, 1)
                : 0,
        ];

        $paymentStats = collect(['en_attente', 'effectue', 'echoue', 'annule', 'rembourse'])
            ->mapWithKeys(fn (string $status) => [$status => [
                'count' => (int) ($paymentStatuses->get($status)?->total ?? 0),
                'amount' => (float) ($paymentStatuses->get($status)?->amount ?? 0),
            ]]);

        $paymentMethodStats = Payment::query()
            ->selectRaw('method, COUNT(*) as total, COALESCE(SUM(amount), 0) as amount')
            ->groupBy('method')
            ->orderByDesc('total')
            ->get()
            ->map(fn (Payment $payment) => [
                'method' => $payment->method ?: 'unknown',
                'count' => (int) $payment->total,
                'amount' => (float) $payment->amount,
            ]);

        $recentTransactions = Payment::query()
            ->with([
                'mission:id,title',
                'payer:id,first_name,last_name',
                'receiver:id,first_name,last_name',
            ])
            ->latest()
            ->limit(10)
            ->get([
                'id', 'mission_id', 'payer_id', 'receiver_id', 'amount', 'platform_fee',
                'provider_amount', 'status', 'method', 'transaction_id', 'created_at',
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
            'transactionStats' => $transactionStats,
            'paymentStats' => $paymentStats,
            'paymentMethodStats' => $paymentMethodStats,
            'recentTransactions' => $recentTransactions,
        ]);
    }
}
