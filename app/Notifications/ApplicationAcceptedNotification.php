<?php

namespace App\Notifications;

use App\Models\Application;
use App\Notifications\Channels\WhatsAppChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApplicationAcceptedNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly Application $application,
        private readonly string $missionUrl,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', WhatsAppChannel::class];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('missions.application.accepted_mail_subject'))
            ->greeting(__('missions.application.accepted_mail_greeting', ['name' => $notifiable->first_name]))
            ->line(__('missions.application.accepted_mail_line', [
                'mission' => $this->application->mission->title,
            ]))
            ->action(__('missions.application.view_mission'), $this->missionUrl);
    }

    public function toWhatsApp(object $notifiable): string
    {
        return __('missions.application.accepted_whatsapp', [
            'name' => $notifiable->first_name,
            'mission' => $this->application->mission->title,
            'url' => $this->missionUrl,
        ]);
    }
}
