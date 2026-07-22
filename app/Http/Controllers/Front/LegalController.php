<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Support\SeoMeta;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LegalController extends Controller
{
    public function show(Request $request, string $document): Response
    {
        $content = config("legal.documents.{$document}");
        abort_unless($content, 404);
        $email = config('legal.contact_email');
        $content['sections'] = collect($content['sections'])->map(fn (array $section) => [
            ...$section,
            'paragraphs' => collect($section['paragraphs'])->map(fn (string $text) => str_replace(':contact_email', $email, $text))->all(),
        ])->all();

        return Inertia::render('Legal/Show', [
            'document' => $content,
            'documentKey' => $document,
            'updatedAt' => config('legal.updated_at'),
            'contactEmail' => $email,
            'seo' => SeoMeta::page($request, $content['title'].' | Barasira', $content['intro']),
        ]);
    }
}
