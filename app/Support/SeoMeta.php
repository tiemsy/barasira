<?php

namespace App\Support;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SeoMeta
{
    /** @var list<string> */
    private const INDEXABLE_ROUTES = [
        'home',
        'front.services.index',
        'front.services.show',
        'front.partners.index',
        'front.partners.sponsorship.create',
        'front.platform-reviews.index',
        'contact.index',
        'legal.show',
    ];

    public static function defaults(Request $request): array
    {
        $name = config('seo.name');
        $url = self::canonical($request);
        $indexable = in_array($request->route()?->getName(), self::INDEXABLE_ROUTES, true);

        return [
            'title' => config('seo.title'),
            'description' => config('seo.description'),
            'canonical' => $url,
            'robots' => $indexable ? 'index,follow,max-image-preview:large' : 'noindex,nofollow',
            'image' => self::absoluteUrl(config('seo.image')),
            'type' => 'website',
            'site_name' => $name,
            'structured_data' => self::organizationGraph($url),
        ];
    }

    public static function page(Request $request, string $title, string $description, array $structuredData = []): array
    {
        $meta = self::defaults($request);
        $meta['title'] = $title;
        $meta['description'] = Str::limit(trim(strip_tags($description)), 160, '…');

        if ($structuredData !== []) {
            $meta['structured_data'] = $structuredData;
        }

        return $meta;
    }

    public static function service(Request $request, Service $service): array
    {
        $location = $service->municipality?->name ?: $service->city?->name ?: config('seo.country');
        $title = "{$service->name} à {$location} | Barasira";
        $description = $service->description ?: "Trouvez un prestataire pour {$service->name} à {$location} sur Barasira.";
        $canonical = self::canonical($request);

        $serviceSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'Service',
            'name' => $service->name,
            'description' => Str::limit(trim(strip_tags($description)), 300, '…'),
            'url' => $canonical,
            'areaServed' => [
                '@type' => 'AdministrativeArea',
                'name' => $location,
            ],
            'provider' => self::organization(),
        ];

        if ($service->category?->name) {
            $serviceSchema['serviceType'] = $service->category->name;
        }

        return self::page($request, $title, $description, [
            '@context' => 'https://schema.org',
            '@graph' => [
                $serviceSchema,
                self::breadcrumbs([
                    ['name' => 'Accueil', 'url' => route('home')],
                    ['name' => 'Services', 'url' => route('front.services.index')],
                    ['name' => $service->name, 'url' => $canonical],
                ]),
            ],
        ]);
    }

    public static function organizationGraph(string $url): array
    {
        return [
            '@context' => 'https://schema.org',
            '@graph' => [
                self::organization(),
                [
                    '@type' => 'WebSite',
                    '@id' => rtrim(config('app.url'), '/').'#website',
                    'url' => rtrim(config('app.url'), '/').'/',
                    'name' => config('seo.name'),
                    'description' => config('seo.description'),
                    'inLanguage' => 'fr',
                    'publisher' => ['@id' => rtrim(config('app.url'), '/').'#organization'],
                ],
            ],
        ];
    }

    public static function breadcrumbs(array $items): array
    {
        return [
            '@type' => 'BreadcrumbList',
            'itemListElement' => collect($items)->values()->map(fn (array $item, int $index) => [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $item['name'],
                'item' => $item['url'],
            ])->all(),
        ];
    }

    private static function organization(): array
    {
        $baseUrl = rtrim(config('app.url'), '/');

        return [
            '@type' => 'Organization',
            '@id' => $baseUrl.'#organization',
            'name' => config('seo.name'),
            'url' => $baseUrl.'/',
            'logo' => self::absoluteUrl(config('seo.image')),
            'description' => config('seo.description'),
            'areaServed' => config('seo.country'),
            'email' => config('mail.contact_address'),
        ];
    }

    private static function canonical(Request $request): string
    {
        return strtok($request->url(), '?') ?: rtrim(config('app.url'), '/').'/';
    }

    private static function absoluteUrl(?string $path): string
    {
        if (Str::startsWith((string) $path, ['http://', 'https://'])) {
            return $path;
        }

        return rtrim(config('app.url'), '/').'/'.ltrim((string) $path, '/');
    }
}
