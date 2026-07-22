<?php

namespace App\Notifications\Channels;

use App\Services\WhatsAppService;
use Illuminate\Notifications\Notification;
use RuntimeException;

class WhatsAppChannel
{
    public function __construct(private readonly WhatsAppService $whatsApp) {}

    public function send(object $notifiable, Notification $notification): void
    {
        if (! method_exists($notification, 'toWhatsApp')) {
            throw new RuntimeException($notification::class.' must define a toWhatsApp method.');
        }

        $phone = method_exists($notifiable, 'routeNotificationForWhatsApp')
            ? $notifiable->routeNotificationForWhatsApp($notification)
            : $notifiable->phone;

        $this->whatsApp->send((string) $phone, (string) $notification->toWhatsApp($notifiable));
    }
}
