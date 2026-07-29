<?php

namespace App\Notifications;

use App\Notifications\Channels\WhatsAppChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;
use Illuminate\Auth\Notifications\VerifyEmail;

class VerifyEmailCustom extends VerifyEmail
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    protected function verificationUrl($notifiable)
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(60),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }

    public function via($notifiable): array
    {
        return ['mail', WhatsAppChannel::class];
    }

    public function toWhatsApp($notifiable): string
    {
        return __('auth.verify_whatsapp', [
            'name' => $notifiable->first_name,
            'url' => $this->verificationUrl($notifiable),
        ]);
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        $url = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject(__('auth.verify_mail_subject'))
            ->greeting(__('auth.verify_mail_greeting', ['name' => $notifiable->first_name]))
            ->line(__('auth.verify_mail_intro'))
            ->line(__('auth.verify_mail_instruction'))
            ->action(__('auth.verify_mail_action'), $url)
            ->line(__('auth.verify_mail_expiry', ['count' => 60]))
            ->salutation(__('auth.verify_mail_salutation'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
