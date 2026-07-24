<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TranslationWorkspaceController extends Controller
{
    public function index(Request $request): Response
    {
        abort_unless($request->user()->isSuperAdmin(), 403);

        return Inertia::render('Admin/Translations/Index', [
            'locales' => ['fr', 'en', 'bm'],
            'provider' => config('ai.translation_provider'),
        ]);
    }
}
