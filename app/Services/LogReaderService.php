<?php

namespace App\Services;

use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
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

    public function purgeAll(): array
    {
        $files = 0;
        $lines = 0;

        foreach ($this->writablePaths() as $path) {
            $content = file_get_contents($path);
            if ($content === false) {
                continue;
            }

            $lines += substr_count($content, "\n") + ($content !== '' && ! str_ends_with($content, "\n") ? 1 : 0);
            if (file_put_contents($path, '', LOCK_EX) !== false) {
                $files++;
            }
        }

        return compact('files', 'lines');
    }

    public function purgeBetween(CarbonInterface $from, CarbonInterface $to): array
    {
        $files = 0;
        $removed = 0;

        foreach ($this->writablePaths() as $path) {
            $input = file($path);
            if ($input === false) {
                continue;
            }

            $output = [];
            $removeBlock = false;
            foreach ($input as $line) {
                if (($timestamp = $this->lineTimestamp($line)) !== null) {
                    $removeBlock = $timestamp->betweenIncluded($from, $to);
                }

                if ($removeBlock) {
                    $removed++;
                } else {
                    $output[] = $line;
                }
            }

            if (file_put_contents($path, implode('', $output), LOCK_EX) !== false) {
                $files++;
            }
        }

        return ['files' => $files, 'lines' => $removed];
    }

    private function writablePaths(): array
    {
        return collect(config('log_viewer.sources', []))
            ->filter(fn ($path) => is_string($path) && is_file($path) && is_writable($path))
            ->unique()
            ->values()
            ->all();
    }

    private function lineTimestamp(string $line): ?Carbon
    {
        foreach ([
            '/\[(\d{4}-\d{2}-\d{2}[ T]\d{2}:\d{2}:\d{2})\]/',
            '/\[(\d{2}\/\w{3}\/\d{4}:\d{2}:\d{2}:\d{2} [+-]\d{4})\]/',
            '/\[(\d{2}-\w{3}-\d{4} \d{2}:\d{2}:\d{2}[^\]]*)\]/',
        ] as $pattern) {
            if (preg_match($pattern, $line, $matches) === 1) {
                try {
                    return Carbon::parse($matches[1], config('app.timezone'));
                } catch (\Throwable) {
                    return null;
                }
            }
        }

        return null;
    }
}
