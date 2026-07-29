<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordResetNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly string $token,
        public readonly string $language,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ]);

        return (new MailMessage)
            ->subject(__('auth.reset_mail_subject', locale: $this->language))
            ->greeting(__('auth.reset_mail_greeting', [
                'name' => $notifiable->first_name,
            ], $this->language))
            ->line(__('auth.reset_mail_intro', locale: $this->language))
            ->action(__('auth.reset_mail_action', locale: $this->language), $url)
            ->line(__('auth.reset_mail_expiry', [
                'count' => config('auth.passwords.users.expire'),
            ], $this->language))
            ->line(__('auth.reset_mail_ignore', locale: $this->language))
            ->salutation(__('auth.reset_mail_salutation', locale: $this->language));
    }
}
