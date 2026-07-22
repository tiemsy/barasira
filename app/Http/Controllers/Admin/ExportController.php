<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mission;
use App\Models\Partner;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    public function users(Request $request): StreamedResponse
    {
        $filters = $request->validate(['search' => ['nullable', 'string', 'max:100'], 'role' => ['nullable', 'in:client,prestataire,admin,superadmin']]);
        $query = User::query()
            ->when($filters['search'] ?? null, fn (Builder $query, string $search) => $query->where(fn (Builder $query) => $query
                ->where('first_name', 'like', "%{$search}%")->orWhere('last_name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")->orWhere('phone', 'like', "%{$search}%")))
            ->when($filters['role'] ?? null, fn (Builder $query, string $role) => $query->where('role', $role))
            ->latest();

        return $this->csv('utilisateurs', ['ID', 'Prénom', 'Nom', 'E-mail', 'Téléphone', 'Rôle', 'Vérifié', 'Créé le'], $query, fn (User $user) => [
            $user->id, $user->first_name, $user->last_name, $user->email, $user->phone, $user->role,
            $user->verified ? 'Oui' : 'Non', $user->created_at?->format('d/m/Y H:i'),
        ]);
    }

    public function services(Request $request): StreamedResponse
    {
        $filters = $request->validate(['search' => ['nullable', 'string', 'max:100'], 'category' => ['nullable', 'integer', 'exists:service_categories,id'], 'status' => ['nullable', 'in:active,inactive']]);
        $query = Service::query()->with(['user', 'category', 'city', 'municipality'])
            ->when($filters['search'] ?? null, fn (Builder $query, string $search) => $query->where(fn (Builder $query) => $query
                ->where('name', 'like', "%{$search}%")->orWhere('description', 'like', "%{$search}%")
                ->orWhereHas('user', fn (Builder $user) => $user->where('first_name', 'like', "%{$search}%")->orWhere('last_name', 'like', "%{$search}%"))))
            ->when($filters['category'] ?? null, fn (Builder $query, int $category) => $query->where('service_category_id', $category))
            ->when($filters['status'] ?? null, fn (Builder $query, string $status) => $query->where('is_active', $status === 'active'))
            ->latest();

        return $this->csv('services', ['ID', 'Service', 'Prestataire', 'Catégorie', 'Ville', 'Commune', 'Prix minimum', 'Prix maximum', 'Statut', 'Créé le'], $query, fn (Service $service) => [
            $service->id, $service->name, trim(($service->user?->first_name ?? '').' '.($service->user?->last_name ?? '')),
            $service->category?->name, $service->city?->name, $service->municipality?->name, $service->price_min,
            $service->price_max, $service->is_active ? 'Actif' : 'Inactif', $service->created_at?->format('d/m/Y H:i'),
        ]);
    }

    public function missions(Request $request): StreamedResponse
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'], 'statuses' => ['nullable', 'array'],
            'statuses.*' => ['in:pending,in_progress,completed,cancelled'], 'date_start' => ['nullable', 'date'],
            'date_end' => ['nullable', 'date'], 'prestataire_id' => ['nullable', 'integer'],
            'price_min' => ['nullable', 'numeric', 'min:0'], 'price_max' => ['nullable', 'numeric', 'min:0'],
            'sort' => ['nullable', 'in:date_asc,date_desc,price_asc,price_desc'],
        ]);
        $query = Mission::query()->with(['client', 'prestataire', 'service'])
            ->when($filters['search'] ?? null, fn (Builder $query, string $search) => $query->where(fn (Builder $query) => $query
                ->where('title', 'like', "%{$search}%")->orWhere('description', 'like', "%{$search}%")->orWhere('address', 'like', "%{$search}%")))
            ->when($filters['statuses'] ?? null, fn (Builder $query, array $statuses) => $query->whereIn('status', $statuses))
            ->when($filters['date_start'] ?? null, fn (Builder $query, string $date) => $query->whereDate('date_start', '>=', $date))
            ->when($filters['date_end'] ?? null, fn (Builder $query, string $date) => $query->whereDate('date_end', '<=', $date))
            ->when($filters['prestataire_id'] ?? null, fn (Builder $query, int $id) => $query->where('prestataire_id', $id))
            ->when(isset($filters['price_min']), fn (Builder $query) => $query->where('price', '>=', $filters['price_min']))
            ->when(isset($filters['price_max']), fn (Builder $query) => $query->where('price', '<=', $filters['price_max']));
        match ($filters['sort'] ?? 'date_desc') {
            'date_asc' => $query->orderBy('date_start'), 'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'), default => $query->orderByDesc('date_start'),
        };

        return $this->csv('missions', ['ID', 'Mission', 'Client', 'Prestataire', 'Service', 'Ville', 'Adresse', 'Statut', 'Prix', 'Début', 'Fin', 'Créée le'], $query, fn (Mission $mission) => [
            $mission->id, $mission->title, trim(($mission->client?->first_name ?? '').' '.($mission->client?->last_name ?? '')),
            trim(($mission->prestataire?->first_name ?? '').' '.($mission->prestataire?->last_name ?? '')), $mission->service?->name,
            $mission->city, $mission->address, $mission->status, $mission->price, $mission->date_start?->format('d/m/Y H:i'),
            $mission->date_end?->format('d/m/Y H:i'), $mission->created_at?->format('d/m/Y H:i'),
        ]);
    }

    public function partners(Request $request): StreamedResponse
    {
        $filters = $request->validate(['search' => ['nullable', 'string', 'max:100'], 'status' => ['nullable', 'in:published,draft']]);
        $query = Partner::query()
            ->when($filters['search'] ?? null, fn (Builder $query, string $search) => $query->where(fn (Builder $query) => $query
                ->where('company_name', 'like', "%{$search}%")->orWhere('contact_name', 'like', "%{$search}%")->orWhere('contact_email', 'like', "%{$search}%")))
            ->when($filters['status'] ?? null, fn (Builder $query, string $status) => $query->where('is_published', $status === 'published'))
            ->orderBy('display_order')->latest();

        return $this->csv('partenaires', ['ID', 'Entreprise', 'E-mail entreprise', 'Téléphone entreprise', 'Site web', 'Adresse', 'Contact', 'Fonction', 'E-mail contact', 'Téléphone contact', 'Statut', 'Créé le'], $query, fn (Partner $partner) => [
            $partner->id, $partner->company_name, $partner->company_email, $partner->company_phone, $partner->website_url,
            $partner->address, $partner->contact_name, $partner->contact_position, $partner->contact_email, $partner->contact_phone,
            $partner->is_published ? 'Publié' : 'Brouillon', $partner->created_at?->format('d/m/Y H:i'),
        ]);
    }

    private function csv(string $name, array $headers, Builder $query, callable $row): StreamedResponse
    {
        return response()->streamDownload(function () use ($headers, $query, $row) {
            $output = fopen('php://output', 'wb');
            fwrite($output, "\xEF\xBB\xBF");
            fputcsv($output, $headers, ';');
            $query->chunkById(500, function ($items) use ($output, $row) {
                foreach ($items as $item) {
                    fputcsv($output, $row($item), ';');
                }
            });
            fclose($output);
        }, $name.'-'.now()->format('Y-m-d-His').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
