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
        return 'Bienvenue sur Barasira, '.$notifiable->first_name.'. Confirmez votre adresse e-mail avec ce lien valable 60 minutes : '.$this->verificationUrl($notifiable);
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        $url = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('Confirmez votre adresse email – Barasira')
            ->greeting('Bienvenue sur Barasira 👋')
            ->line('Merci de vous être inscrit sur Barasira.')
            ->line('Veuillez confirmer votre adresse email pour activer votre compte.')
            ->action('Vérifier mon email', $url)
            ->line('Ce lien expire dans 60 minutes.')
            ->salutation('— L’équipe Barasira');
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
