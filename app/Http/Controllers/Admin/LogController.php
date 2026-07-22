<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\LogReaderService;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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

    public function purge(Request $request): RedirectResponse
    {
        abort_unless($request->user()->isSuperAdmin(), 403);

        $data = $request->validate([
            'mode' => ['required', Rule::in(['all', 'period'])],
            'date_from' => ['nullable', 'required_if:mode,period', 'date_format:Y-m-d'],
            'date_to' => ['nullable', 'required_if:mode,period', 'date_format:Y-m-d', 'after_or_equal:date_from'],
        ]);

        $result = $data['mode'] === 'all'
            ? $this->logReader->purgeAll()
            : $this->logReader->purgeBetween(
                CarbonImmutable::createFromFormat('Y-m-d', $data['date_from'], config('app.timezone'))->startOfDay(),
                CarbonImmutable::createFromFormat('Y-m-d', $data['date_to'], config('app.timezone'))->endOfDay(),
            );

        Log::channel('audit')->warning('logs.purged', [
            'actor_id' => $request->user()->id,
            'actor_email' => $request->user()->email,
            'mode' => $data['mode'],
            'date_from' => $data['date_from'] ?? null,
            'date_to' => $data['date_to'] ?? null,
            ...$result,
        ]);

        return back()->with('success', __('messages.logs_purged', ['count' => $result['lines']]));
    }
}
