<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DatabaseBrowserService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class DatabaseBrowserController extends Controller
{
    public function __construct(private readonly DatabaseBrowserService $browser) {}

    public function index(Request $request): Response
    {
        abort_unless($request->user()->isSuperAdmin(), 403);

        $tables = $this->browser->tables();
        $validated = $request->validate([
            'table' => ['nullable', Rule::in($tables)],
            'per_page' => ['nullable', 'integer', Rule::in([25, 50, 100])],
        ]);
        $table = $validated['table'] ?? ($tables[0] ?? null);
        $perPage = (int) ($validated['per_page'] ?? 50);

        return Inertia::render('Admin/Database/Index', [
            'tables' => $tables,
            'selectedTable' => $table,
            'columns' => $table ? $this->browser->columns($table) : [],
            'rows' => $table ? $this->browser->rows($table, $perPage)->withQueryString() : null,
            'perPage' => $perPage,
        ]);
    }
}
