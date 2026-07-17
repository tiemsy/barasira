<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\LogReaderService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class LogController extends Controller
{
    public function __construct(private readonly LogReaderService $logReader) {}

    public function index(Request $request): Response
    {
        abort_unless($request->user()->isSuperAdmin(), 403);

        $sourceKeys = array_keys(config('log_viewer.sources', []));
        $validated = $request->validate([
            'source' => ['nullable', Rule::in($sourceKeys)],
            'lines' => ['nullable', 'integer', 'min:25', 'max:500'],
        ]);

        $source = $validated['source'] ?? 'laravel';
        $lines = (int) ($validated['lines'] ?? 200);

        return Inertia::render('Admin/Logs/Index', [
            'sources' => $this->logReader->sources(),
            'selectedSource' => $source,
            'selectedLines' => $lines,
            'entries' => $this->logReader->tail($source, $lines),
        ]);
    }
}
