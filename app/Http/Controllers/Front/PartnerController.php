<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use App\Support\SeoMeta;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PartnerController extends Controller
{
    public function index(Request $request): Response
    {
        return Inertia::render('Partners/Index', [
            'seo' => SeoMeta::page(
                $request,
                'Partenaires de Barasira au Mali',
                'Découvrez les entreprises et organisations partenaires qui accompagnent Barasira au Mali.',
                [
                    '@context' => 'https://schema.org',
                    '@graph' => [SeoMeta::breadcrumbs([
                        ['name' => 'Accueil', 'url' => route('home')],
                        ['name' => 'Partenaires', 'url' => route('front.partners.index')],
                    ])],
                ]
            ),
            'partners' => Partner::query()->published()->get(['id', 'company_name', 'description', 'logo_path', 'website_url']),
        ]);
    }
}
