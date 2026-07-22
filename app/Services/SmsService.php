<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class SmsService
{
    public function send(string $phone, string $message): void
    {
        if (config('sms.driver') === 'log') {
            Log::info('SMS notification', ['to' => $phone, 'message' => $message]);

            return;
        }

        if (config('sms.driver') !== 'http' || ! config('sms.endpoint')) {
            throw new RuntimeException('The SMS gateway is not configured.');
        }

        Http::withToken((string) config('sms.token'))
            ->timeout(10)
            ->post(config('sms.endpoint'), [
                'to' => $phone,
                'message' => $message,
                'sender' => config('sms.sender'),
            ])
            ->throw();
    }
}
