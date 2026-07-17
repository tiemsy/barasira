<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        // Les applications natives s'authentifient avec un jeton Sanctum et ne
        // disposent pas du cookie CSRF d'un navigateur.
        'api/login',
        'api/register',
    ];
}
