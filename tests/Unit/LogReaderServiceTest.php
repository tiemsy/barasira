<?php

namespace Tests\Unit;

use App\Services\LogReaderService;
use Carbon\CarbonImmutable;
use Tests\TestCase;

class LogReaderServiceTest extends TestCase
{
    private array $paths = [];

    protected function tearDown(): void
    {
        foreach ($this->paths as $path) {
            @unlink($path);
        }

        parent::tearDown();
    }

    public function test_it_purges_only_entries_in_the_selected_period(): void
    {
        $path = $this->logFile("[2026-07-20 10:00:00] local.INFO: keep\n[2026-07-22 10:00:00] local.ERROR: remove\nstack line\n[2026-07-24 10:00:00] local.INFO: keep too\n");
        config(['log_viewer.sources' => ['laravel' => $path]]);

        $result = app(LogReaderService::class)->purgeBetween(
            CarbonImmutable::parse('2026-07-22 00:00:00'),
            CarbonImmutable::parse('2026-07-22 23:59:59'),
        );

        $this->assertSame(2, $result['lines']);
        $this->assertStringNotContainsString('remove', file_get_contents($path));
        $this->assertStringNotContainsString('stack line', file_get_contents($path));
        $this->assertStringContainsString('keep too', file_get_contents($path));
    }

    public function test_it_can_empty_every_configured_log(): void
    {
        $first = $this->logFile("first\n");
        $second = $this->logFile("second\n");
        config(['log_viewer.sources' => ['first' => $first, 'second' => $second]]);

        $result = app(LogReaderService::class)->purgeAll();

        $this->assertSame(['files' => 2, 'lines' => 2], $result);
        $this->assertSame('', file_get_contents($first));
        $this->assertSame('', file_get_contents($second));
    }

    private function logFile(string $content): string
    {
        $path = sys_get_temp_dir().'/barasira-log-'.uniqid().'.log';
        file_put_contents($path, $content);
        $this->paths[] = $path;

        return $path;
    }
}
