<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProviderDocumentController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'document_type' => ['required', Rule::in(['identity', 'diploma', 'certification', 'address_proof', 'other'])],
            'label' => ['nullable', 'required_if:document_type,other', 'string', 'max:150'],
            'file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:10240'],
        ]);
        $file = $request->file('file');
        $path = $file->store("provider-documents/{$request->user()->id}", 'local');

        $request->user()->documents()->create([
            'document_type' => $data['document_type'], 'label' => $data['label'] ?? null,
            'file_url' => $path, 'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(), 'file_size' => $file->getSize(),
            'status' => 'en_attente', 'uploaded_at' => now(),
        ]);

        return back()->with('success', __('messages.document_uploaded'));
    }

    public function download(Request $request, Document $document): StreamedResponse
    {
        abort_unless($document->user_id === $request->user()->id, 403);
        abort_unless(Storage::disk('local')->exists($document->file_url), 404);

        return Storage::disk('local')->download($document->file_url, $document->original_name ?: 'document');
    }

    public function destroy(Request $request, Document $document): RedirectResponse
    {
        abort_unless($document->user_id === $request->user()->id, 403);
        Storage::disk('local')->delete($document->file_url);
        $wasIdentityDocument = $document->document_type === 'identity';
        $user = $document->user;
        $document->delete();
        if ($wasIdentityDocument) {
            $user->syncIdentityVerification();
        }

        return back()->with('success', __('messages.document_deleted'));
    }
}
