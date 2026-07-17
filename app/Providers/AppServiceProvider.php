<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configurePhpErrorLog();

        RateLimiter::for('ai', function (Request $request) {
            return [
                Limit::perMinute(10)->by($request->user()?->id ?: $request->ip()),
                Limit::perDay(100)->by($request->user()?->id ?: $request->ip()),
            ];
        });
    }

    private function configurePhpErrorLog(): void
    {
        if (! config('log_viewer.configure_php_logging')) {
            return;
        }

        $path = config('log_viewer.sources.php');
        if (! is_string($path) || $path === '') {
            $path = storage_path('logs/php-error.log');
        }

        $directory = dirname($path);
        if (! is_dir($directory) || ! is_writable($directory)) {
            $path = storage_path('logs/php-error.log');
            $directory = dirname($path);
        }

        if (! is_dir($directory) || ! is_writable($directory)) {
            return;
        }

        config(['log_viewer.sources.php' => $path]);

        if (! is_file($path)) {
            file_put_contents($path, '', LOCK_EX);
            @chmod($path, 0664);
        }

        if (is_writable($path)) {
            ini_set('log_errors', '1');
            ini_set('error_log', $path);
        }
    }
}
