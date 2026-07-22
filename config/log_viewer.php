<?php

return [
    'max_lines' => 500,
    'configure_php_logging' => env('LOG_VIEWER_CONFIGURE_PHP_LOGGING', true),

    'sources' => [
        'audit' => env('AUDIT_LOG_PATH', storage_path('logs/audit.log')),
        'laravel' => env('LOG_VIEWER_LARAVEL_PATH', storage_path('logs/laravel.log')),
        'php' => env('LOG_VIEWER_PHP_PATH', storage_path('logs/php-error.log')),
        'nginx_access' => env('LOG_VIEWER_NGINX_ACCESS_PATH', '/var/log/nginx/access.log'),
        'nginx_error' => env('LOG_VIEWER_NGINX_ERROR_PATH', '/var/log/nginx/error.log'),
    ],
];
