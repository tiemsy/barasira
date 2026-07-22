<?php

namespace Tests\Feature;

use App\Mail\PartnerSponsorshipMail;
use Illuminate\Support\Facades\Mail;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class PartnerSponsorshipTest extends TestCase
{
    public function test_sponsorship_page_offers_the_three_configured_plans(): void
    {
        $this->get('/partners/sponsoring')->assertOk()->assertInertia(fn (Assert $page) => $page
            ->component('Partners/Sponsorship')
            ->has('plans', 3)
            ->where('plans.0.id', 'week')
            ->where('plans.0.price', 50000)
            ->where('plans.1.id', 'month')
            ->where('plans.1.price', 150000)
            ->where('plans.2.id', 'quarter')
            ->where('plans.2.price', 350000));
    }

    public function test_partner_can_email_a_sponsorship_request_for_the_selected_plan(): void
    {
        Mail::fake();
        config(['partner_sponsorship.recipient' => 'partenaires@barasira.com']);

        $response = $this->post('/partners/sponsoring', [
            'company_name' => 'Entreprise Mali',
            'contact_name' => 'Awa Traoré',
            'email' => 'awa@example.com',
            'phone' => '+22370000000',
            'website_url' => 'https://example.com',
            'category' => 'commerce',
            'plan' => 'month',
            'message' => 'Publication pour notre lancement.',
            'consent' => true,
        ]);

        $response->assertSessionHasNoErrors()->assertSessionHas('success');
        Mail::assertSent(PartnerSponsorshipMail::class, function (PartnerSponsorshipMail $mail) {
            return $mail->hasTo('partenaires@barasira.com')
                && $mail->requestData['plan'] === 'month'
                && $mail->plan['price'] === 150000;
        });
    }

    public function test_sponsorship_request_rejects_an_unknown_plan(): void
    {
        Mail::fake();

        $this->post('/partners/sponsoring', [
            'company_name' => 'Entreprise Mali',
            'contact_name' => 'Awa Traoré',
            'email' => 'awa@example.com',
            'phone' => '+22370000000',
            'category' => 'commerce',
            'plan' => 'custom',
            'consent' => true,
        ])->assertSessionHasErrors('plan');

        Mail::assertNothingSent();
    }
}
