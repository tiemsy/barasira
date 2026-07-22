<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Requests\PartnerSponsorshipRequest;
use App\Mail\PartnerSponsorshipMail;
use App\Support\SeoMeta;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class PartnerSponsorshipController extends Controller
{
    public function create(Request $request): Response
    {
        return Inertia::render('Partners/Sponsorship', [
            'plans' => collect(config('partner_sponsorship.plans'))->map(fn (array $plan, string $id) => [
                'id' => $id,
                ...$plan,
            ])->values(),
            'categories' => config('partner_sponsorship.categories'),
            'seo' => SeoMeta::page(
                $request,
                'Publication sponsorisée pour les partenaires | Barasira',
                'Mettez votre entreprise en avant sur Barasira pendant une semaine, un mois ou trois mois.'
            ),
        ]);
    }

    public function store(PartnerSponsorshipRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $plan = config("partner_sponsorship.plans.{$data['plan']}");

        try {
            Mail::to(config('partner_sponsorship.recipient'))
                ->send(new PartnerSponsorshipMail($data, $plan));

            return back()->with('success', __('messages.sponsorship_request_sent'));
        } catch (Throwable $exception) {
            Log::error('Partner sponsorship request email failed.', [
                'company' => $data['company_name'],
                'email' => $data['email'],
                'exception' => $exception->getMessage(),
            ]);

            return back()->with('error', __('messages.sponsorship_request_failed'));
        }
    }
}
