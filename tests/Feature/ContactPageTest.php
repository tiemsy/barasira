<?php

namespace Tests\Feature;

use App\Mail\ContactMessageMail;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ContactPageTest extends TestCase
{
    public function test_contact_message_can_be_sent_and_success_is_shared_with_inertia(): void
    {
        Mail::fake();

        $response = $this->from('/contact-us')->post('/contact-us', [
            'name' => 'Aminata Traoré',
            'email' => 'aminata@example.com',
            'phone' => '+223 70 00 00 00',
            'user_type' => 'client',
            'subject' => 'Question sur une mission',
            'message' => 'Je souhaite obtenir des informations sur ma mission.',
            'consent' => true,
        ]);

        $response
            ->assertRedirect('/contact-us')
            ->assertSessionHas('success');

        Mail::assertSent(ContactMessageMail::class);

        $this->get('/contact-us')
            ->assertInertia(fn ($page) => $page
                ->component('ContactUs')
                ->where('flash.success', 'Votre message a bien été envoyé. Notre équipe vous répondra rapidement.')
            );
    }
}
