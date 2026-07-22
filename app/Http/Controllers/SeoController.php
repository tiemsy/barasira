<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Response;

class SeoController extends Controller
{
    public function sitemap(): Response
    {
        $urls = collect([
            ['location' => route('home'), 'changefreq' => 'daily', 'priority' => '1.0'],
            ['location' => route('front.services.index'), 'changefreq' => 'daily', 'priority' => '0.9'],
            ['location' => route('front.partners.index'), 'changefreq' => 'weekly', 'priority' => '0.6'],
            ['location' => route('front.platform-reviews.index'), 'changefreq' => 'weekly', 'priority' => '0.7'],
            ['location' => route('contact.index'), 'changefreq' => 'monthly', 'priority' => '0.5'],
            ...collect(['cgu', 'cgv', 'confidentialite', 'cookies', 'moderation', 'kyc'])->map(fn (string $document) => [
                'location' => route('legal.show', $document), 'changefreq' => 'yearly', 'priority' => '0.3',
            ])->all(),
        ])->merge(
            Service::query()
                ->where('is_active', true)
                ->whereNotNull('slug')
                ->latest('updated_at')
                ->get(['slug', 'updated_at'])
                ->map(fn (Service $service) => [
                    'location' => route('front.services.show', $service),
                    'lastmod' => $service->updated_at?->toAtomString(),
                    'changefreq' => 'weekly',
                    'priority' => '0.8',
                ])
        );

        return response()
            ->view('seo.sitemap', ['urls' => $urls])
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}
