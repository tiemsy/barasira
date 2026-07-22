<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PartnerRequest;
use App\Models\Partner;
use App\Models\PartnerPromotion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class PartnerController extends Controller
{
    public function index(Request $request): Response
    {
        $filters = $request->validate(['search' => ['nullable', 'string', 'max:100'], 'status' => ['nullable', 'in:published,draft']]);

        return Inertia::render('Admin/Partners/Index', [
            'partners' => Partner::query()
                ->when($filters['search'] ?? null, fn ($query, $search) => $query->where(fn ($query) => $query
                    ->where('company_name', 'like', "%{$search}%")
                    ->orWhere('contact_name', 'like', "%{$search}%")
                    ->orWhere('contact_email', 'like', "%{$search}%")))
                ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('is_published', $status === 'published'))
                ->orderBy('display_order')->latest()->paginate(12)->withQueryString(),
            'filters' => $filters,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Partners/Form');
    }

    public function store(PartnerRequest $request): RedirectResponse
    {
        $data = $this->data($request);
        $data['created_by'] = $request->user()->id;
        $data['updated_by'] = $request->user()->id;
        $partner = Partner::query()->create($data);
        $this->syncPromotion($request, $partner);

        return redirect()->route('admin.partners.index')->with('success', __('messages.partner_created'));
    }

    public function edit(Partner $partner): Response
    {
        return Inertia::render('Admin/Partners/Form', [
            'partner' => $partner,
            'promotion' => $partner->promotions()->latest('starts_at')->first(),
        ]);
    }

    public function update(PartnerRequest $request, Partner $partner): RedirectResponse
    {
        $oldLogo = $partner->logo_path;
        $data = $this->data($request);
        $data['updated_by'] = $request->user()->id;
        $partner->update($data);
        $this->syncPromotion($request, $partner);
        if ($request->hasFile('logo') && $oldLogo) {
            Storage::disk('public')->delete($oldLogo);
        }

        return redirect()->route('admin.partners.index')->with('success', __('messages.partner_updated'));
    }

    public function destroy(Partner $partner): RedirectResponse
    {
        if ($partner->logo_path) {
            Storage::disk('public')->delete($partner->logo_path);
        }
        $partner->delete();

        return back()->with('success', __('messages.partner_deleted'));
    }

    private function data(PartnerRequest $request): array
    {
        $data = $request->safe()->except([
            'logo', 'promotion_id', 'promotion_amount', 'promotion_starts_at', 'promotion_ends_at',
        ]);
        if ($request->hasFile('logo')) {
            $data['logo_path'] = $request->file('logo')->store('partners', 'public');
        }

        return $data;
    }

    private function syncPromotion(PartnerRequest $request, Partner $partner): void
    {
        if (! $request->filled(['promotion_amount', 'promotion_starts_at', 'promotion_ends_at'])) {
            return;
        }

        $promotion = $request->filled('promotion_id')
            ? PartnerPromotion::query()->where('partner_id', $partner->id)->findOrFail($request->integer('promotion_id'))
            : new PartnerPromotion(['partner_id' => $partner->id, 'created_by' => $request->user()->id]);

        $promotion->fill([
            'paid_amount' => $request->input('promotion_amount'),
            'starts_at' => $request->date('promotion_starts_at'),
            'ends_at' => $request->date('promotion_ends_at'),
        ])->save();
    }
}
