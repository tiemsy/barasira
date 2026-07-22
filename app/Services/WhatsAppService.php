<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class WhatsAppService
{
    public function send(string $phone, string $message): void
    {
        $phone = preg_replace('/[^0-9+]/', '', $phone);
        if (blank($phone)) {
            throw new RuntimeException('The WhatsApp recipient has no valid phone number.');
        }

        if (config('whatsapp.driver') === 'log') {
            Log::info('WhatsApp notification', ['to' => $phone, 'message' => $message]);

            return;
        }

        if (config('whatsapp.driver') !== 'http' || ! config('whatsapp.endpoint')) {
            throw new RuntimeException('The WhatsApp gateway is not configured.');
        }

        Http::withToken((string) config('whatsapp.token'))
            ->timeout(10)
            ->post(config('whatsapp.endpoint'), [
                'to' => $phone,
                'message' => $message,
                'sender' => config('whatsapp.sender'),
            ])
            ->throw();
    }
}
