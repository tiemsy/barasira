<?php

namespace App\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DatabaseBrowserService
{
    private const SENSITIVE_FRAGMENTS = [
        'password', 'remember_token', 'token', 'secret', 'api_key', 'private_key',
        'access_key', 'credential', 'authorization',
    ];

    public function tables(): array
    {
        $tables = DB::connection()->getSchemaBuilder()->getTableListing();
        sort($tables, SORT_NATURAL | SORT_FLAG_CASE);

        return array_values($tables);
    }

    public function columns(string $table): array
    {
        return collect(DB::connection()->getSchemaBuilder()->getColumns($table))->map(fn (array $column) => [
            'name' => $column['name'],
            'type' => $column['type_name'] ?? $column['type'] ?? 'unknown',
            'nullable' => (bool) ($column['nullable'] ?? false),
        ])->all();
    }

    public function rows(string $table, int $perPage): LengthAwarePaginator
    {
        $query = DB::table($table);
        $columns = DB::connection()->getSchemaBuilder()->getColumnListing($table);
        if (in_array('id', $columns, true)) {
            $query->orderBy('id');
        }

        return $query->paginate($perPage)->through(function (object $row) {
            return collect((array) $row)->mapWithKeys(fn ($value, string $column) => [
                $column => $this->isSensitive($column) ? '[MASQUÉ]' : $this->normalize($value),
            ])->all();
        });
    }

    private function isSensitive(string $column): bool
    {
        return Str::contains(strtolower($column), self::SENSITIVE_FRAGMENTS);
    }

    private function normalize(mixed $value): mixed
    {
        if (! is_string($value)) {
            return $value;
        }

        return mb_check_encoding($value, 'UTF-8') ? $value : '[DONNÉE BINAIRE]';
    }
}
