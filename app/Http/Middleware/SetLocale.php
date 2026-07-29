<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    private const SUPPORTED_LOCALES = ['fr', 'en', 'bm'];

    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->cookie('barasira_locale', config('app.locale'));
        $locale = in_array($locale, self::SUPPORTED_LOCALES, true)
            ? $locale
            : config('app.locale');

        app()->setLocale($locale);

        if ($request->user() && $request->user()->locale !== $locale) {
            $request->user()->forceFill(['locale' => $locale])->saveQuietly();
        }

        return $next($request);
    }
}
