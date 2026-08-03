<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\VerifyEmailCustom;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationLocaleTest extends TestCase
{
    use RefreshDatabase;

    public function test_selected_locale_is_saved_as_the_user_preference(): void
    {
        $user = User::factory()->create(['locale' => 'fr']);

        $this->actingAs($user)
            ->withUnencryptedCookie('BaraSira_locale', 'en')
            ->get('/')
            ->assertOk();

        $this->assertSame('en', $user->fresh()->locale);
    }

    public function test_verification_notification_uses_recipient_locale(): void
    {
        $user = User::factory()->create([
            'first_name' => 'Awa',
            'locale' => 'en',
        ]);

        $previousLocale = app()->getLocale();

        try {
            app()->setLocale($user->preferredLocale());
            $mail = (new VerifyEmailCustom)->toMail($user);
        } finally {
            app()->setLocale($previousLocale);
        }

        $this->assertSame('Confirm your email address – BaraSira', $mail->subject);
        $this->assertSame('Welcome to BaraSira, Awa 👋', $mail->greeting);
    }
}
