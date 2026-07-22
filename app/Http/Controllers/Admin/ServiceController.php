<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreServiceRequest;
use App\Http\Requests\Admin\UpdateServiceRequest;
use App\Models\City;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class ServiceController extends Controller
{
    public function index(Request $request): Response
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'integer', 'exists:service_categories,id'],
            'status' => ['nullable', 'in:active,inactive'],
        ]);

        $services = Service::query()
            ->with(['user:id,first_name,last_name', 'category:id,name', 'city:id,name'])
            ->when($filters['search'] ?? null, function ($query, string $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhereHas('user', fn ($user) => $user
                            ->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%"));
                });
            })
            ->when($filters['category'] ?? null, fn ($query, int $category) => $query->where('service_category_id', $category))
            ->when($filters['status'] ?? null, fn ($query, string $status) => $query->where('is_active', $status === 'active'))
            ->latest()
            ->select([
                'id', 'user_id', 'service_category_id', 'city_id', 'name', 'icon',
                'price_min', 'price_max', 'is_active', 'created_at',
            ])
            ->paginate(12)
            ->withQueryString();

        return Inertia::render('Admin/Services/Index', [
            'services' => $services,
            'categories' => ServiceCategory::query()->orderBy('name')->get(['id', 'name']),
            'filters' => $filters,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Services/Form', $this->formData());
    }

    public function store(StoreServiceRequest $request): RedirectResponse
    {
        Service::query()->create($request->validated());

        return redirect()->route('admin.services.index')->with('success', __('Service créé avec succès.'));
    }

    public function edit(Service $service): Response
    {
        return Inertia::render('Admin/Services/Form', $this->formData($service));
    }

    public function update(UpdateServiceRequest $request, Service $service): RedirectResponse
    {
        $service->update($request->validated());

        return redirect()->route('admin.services.index')->with('success', __('Service mis à jour avec succès.'));
    }

    public function destroy(Service $service): RedirectResponse
    {
        if ($service->missions()->exists()) {
            throw ValidationException::withMessages([
                'service' => __('Ce service est lié à une mission et ne peut pas être supprimé. Désactivez-le plutôt.'),
            ]);
        }

        $service->delete();

        return redirect()->route('admin.services.index')->with('success', __('Service supprimé avec succès.'));
    }

    private function formData(?Service $service = null): array
    {
        return [
            'service' => $service?->only([
                'id', 'user_id', 'service_category_id', 'city_id', 'municipality_id', 'name',
                'description', 'icon', 'price_min', 'price_max', 'is_active',
            ]),
            'providers' => User::query()
                ->where('role', 'prestataire')
                ->orderBy('first_name')
                ->get(['id', 'first_name', 'last_name']),
            'categories' => ServiceCategory::query()->orderBy('name')->get(['id', 'name']),
            'cities' => City::query()
                ->with(['municipalities:id,city_id,name'])
                ->orderBy('name')
                ->get(['id', 'name']),
        ];
    }
}
