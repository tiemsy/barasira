<?php

namespace App\Services;

use SplFileObject;

class LogReaderService
{
    public function sources(): array
    {
        return collect(config('log_viewer.sources', []))
            ->map(fn (string $path, string $key) => [
                'key' => $key,
                'available' => is_file($path) && is_readable($path),
                'size' => is_file($path) ? filesize($path) : null,
                'updated_at' => is_file($path) ? filemtime($path) : null,
            ])
            ->values()
            ->all();
    }

    public function tail(string $source, int $lines): array
    {
        $path = config("log_viewer.sources.{$source}");

        if (! is_string($path) || ! is_file($path) || ! is_readable($path)) {
            return [];
        }

        $limit = min(max($lines, 1), (int) config('log_viewer.max_lines', 500));
        $file = new SplFileObject($path, 'r');
        $file->seek(PHP_INT_MAX);
        $lastLine = $file->key();
        $startLine = max(0, $lastLine - $limit);
        $entries = [];

        $file->seek($startLine);
        while (! $file->eof()) {
            $line = rtrim((string) $file->current(), "\r\n");
            if ($line !== '') {
                $entries[] = ['number' => $file->key() + 1, 'content' => $line];
            }
            $file->next();
        }

        return array_slice($entries, -$limit);
    }
}
