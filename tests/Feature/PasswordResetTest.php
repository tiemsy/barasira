<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\PasswordResetNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_request_a_password_reset_link(): void
    {
        Notification::fake();
        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email, 'locale' => 'fr'])
            ->assertRedirect()
            ->assertSessionHas('success');

        Notification::assertSentTo(
            $user,
            PasswordResetNotification::class,
            fn (PasswordResetNotification $notification) => $notification->language === 'fr'
                && $notification->toMail($user)->subject === 'Réinitialisation de votre mot de passe Barasira'
        );
    }

    public function test_user_can_reset_their_password_with_a_valid_token(): void
    {
        Notification::fake();
        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email, 'locale' => 'fr']);

        Notification::assertSentTo(
            $user,
            PasswordResetNotification::class,
            function (PasswordResetNotification $notification) use ($user): bool {
                $response = $this->post('/reset-password', [
                    'token' => $notification->token,
                    'email' => $user->email,
                    'password' => 'Nouveau-mot-de-passe-2026',
                    'password_confirmation' => 'Nouveau-mot-de-passe-2026',
                ]);

                $response->assertRedirect('/login')->assertSessionHas('success');

                return Hash::check('Nouveau-mot-de-passe-2026', $user->fresh()->password);
            }
        );
    }

    public function test_invalid_token_cannot_reset_password(): void
    {
        $user = User::factory()->create();
        $oldPassword = $user->password;

        $this->post('/reset-password', [
            'token' => 'invalid-token',
            'email' => $user->email,
            'password' => 'Nouveau-mot-de-passe-2026',
            'password_confirmation' => 'Nouveau-mot-de-passe-2026',
        ])->assertSessionHasErrors('email');

        $this->assertSame($oldPassword, $user->fresh()->password);
    }
}
