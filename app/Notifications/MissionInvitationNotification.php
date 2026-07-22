<?php

namespace App\Notifications;

use App\Notifications\Channels\WhatsAppChannel;
use App\Models\MissionInvitation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MissionInvitationNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly MissionInvitation $invitation,
        private readonly string $validationUrl,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', WhatsAppChannel::class];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mission = $this->invitation->mission;
        $client = $this->invitation->client;

        return (new MailMessage)
            ->subject(__('missions.invitation.mail_subject'))
            ->greeting(__('missions.invitation.mail_greeting', ['name' => $notifiable->first_name]))
            ->line(__('missions.invitation.mail_line', [
                'client' => trim($client->first_name.' '.$client->last_name),
                'mission' => $mission->title,
            ]))
            ->line(__('missions.invitation.mail_expiry', ['hours' => 48]))
            ->action(__('missions.invitation.validate_action'), $this->validationUrl)
            ->line(__('missions.invitation.mail_security'));
    }

    public function toWhatsApp(object $notifiable): string
    {
        return __('missions.invitation.whatsapp', [
            'name' => $notifiable->first_name,
            'client' => trim($this->invitation->client->first_name.' '.$this->invitation->client->last_name),
            'mission' => $this->invitation->mission->title,
            'url' => $this->validationUrl,
        ]);
    }
}
