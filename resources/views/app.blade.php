<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @php($seo = $page['props']['seo'] ?? [])
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title inertia="title">{{ $seo['title'] ?? config('seo.title') }}</title>
        <meta inertia="description" name="description" content="{{ $seo['description'] ?? config('seo.description') }}">
        <meta inertia="robots" name="robots" content="{{ $seo['robots'] ?? 'noindex,nofollow' }}">
        <link inertia="canonical" rel="canonical" href="{{ $seo['canonical'] ?? url()->current() }}">
        <meta property="og:locale" content="fr_FR">
        <meta inertia="og:type" property="og:type" content="{{ $seo['type'] ?? 'website' }}">
        <meta inertia="og:site_name" property="og:site_name" content="{{ $seo['site_name'] ?? config('seo.name') }}">
        <meta inertia="og:title" property="og:title" content="{{ $seo['title'] ?? config('seo.title') }}">
        <meta inertia="og:description" property="og:description" content="{{ $seo['description'] ?? config('seo.description') }}">
        <meta inertia="og:url" property="og:url" content="{{ $seo['canonical'] ?? url()->current() }}">
        <meta inertia="og:image" property="og:image" content="{{ $seo['image'] ?? asset(config('seo.image')) }}">
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ $seo['title'] ?? config('seo.title') }}">
        <meta name="twitter:description" content="{{ $seo['description'] ?? config('seo.description') }}">
        <meta name="twitter:image" content="{{ $seo['image'] ?? asset(config('seo.image')) }}">
        @if (! empty($seo['structured_data']))
            <script inertia="structured-data" type="application/ld+json">{!! json_encode($seo['structured_data'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>
        @endif
        @vite(['resources/js/app.js', 'resources/scss/app.scss'])
        @inertiaHead
    </head>
    <body>
        @inertia
    </body>
</html>
