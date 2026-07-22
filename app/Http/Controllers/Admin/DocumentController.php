<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\ServiceCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    public function index(Request $request): Response
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'provider' => ['nullable', 'string', 'max:100'],
            'service_category' => ['nullable', 'integer', 'exists:service_categories,id'],
            'status' => ['nullable', Rule::in(['en_attente', 'valide', 'rejete'])],
            'type' => ['nullable', Rule::in(['identity', 'diploma', 'certification', 'address_proof', 'other'])],
        ]);

        return Inertia::render('Admin/Documents/Index', [
            'documents' => Document::query()->with(['user:id,first_name,last_name,email,identity_verified_at', 'reviewer:id,first_name,last_name'])
                ->whereHas('user', fn ($query) => $query->where('role', 'prestataire'))
                ->when($filters['search'] ?? null, fn ($query, string $search) => $query->where(fn ($query) => $query
                    ->where('original_name', 'like', "%{$search}%")->orWhere('label', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($user) => $user->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"))))
                ->when($filters['provider'] ?? null, function ($query, string $provider) {
                    $terms = preg_split('/\s+/u', trim($provider), -1, PREG_SPLIT_NO_EMPTY);
                    $query->whereHas('user', function ($user) use ($terms) {
                        foreach ($terms as $term) {
                            $user->where(fn ($user) => $user
                                ->where('first_name', 'like', "%{$term}%")
                                ->orWhere('last_name', 'like', "%{$term}%"));
                        }
                    });
                })
                ->when($filters['service_category'] ?? null, fn ($query, int $category) => $query
                    ->whereHas('user.providerServices', fn ($services) => $services->where('service_category_id', $category)))
                ->when($filters['status'] ?? null, fn ($query, string $status) => $query->where('status', $status))
                ->when($filters['type'] ?? null, fn ($query, string $type) => $query->where('document_type', $type))
                ->orderByRaw("CASE WHEN status = 'en_attente' THEN 0 ELSE 1 END")
                ->latest('uploaded_at')->paginate(15)->withQueryString(),
            'filters' => $filters,
            'serviceCategories' => ServiceCategory::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function download(Document $document): StreamedResponse
    {
        abort_unless($document->user?->role === 'prestataire', 404);
        abort_unless(Storage::disk('local')->exists($document->file_url), 404);

        return Storage::disk('local')->download($document->file_url, $document->original_name ?: 'document');
    }

    public function review(Request $request, Document $document): RedirectResponse
    {
        abort_unless($document->user?->role === 'prestataire', 404);
        $data = $request->validate([
            'status' => ['required', Rule::in(['en_attente', 'valide', 'rejete'])],
            'review_comment' => ['nullable', Rule::requiredIf($request->input('status') === 'rejete'), 'string', 'max:1000'],
        ]);
        $document->update([
            'status' => $data['status'],
            'review_comment' => $data['status'] === 'rejete' ? $data['review_comment'] : null,
            'reviewed_by' => $data['status'] === 'en_attente' ? null : $request->user()->id,
            'reviewed_at' => $data['status'] === 'en_attente' ? null : now(),
        ]);
        if ($document->document_type === 'identity') {
            $document->user->syncIdentityVerification();
        }

        return back()->with('success', __('messages.document_reviewed'));
    }

    public function destroy(Document $document): RedirectResponse
    {
        abort_unless($document->user?->role === 'prestataire', 404);

        $user = $document->user;
        $isIdentityDocument = $document->document_type === 'identity';

        Storage::disk('local')->delete($document->file_url);
        $document->delete();

        if ($isIdentityDocument) {
            $user->syncIdentityVerification();
        }

        return back()->with('success', __('messages.document_deleted'));
    }
}
