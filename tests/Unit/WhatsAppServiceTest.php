<?php

namespace Tests\Unit;

use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WhatsAppServiceTest extends TestCase
{
    public function test_http_driver_sends_the_expected_whatsapp_payload(): void
    {
        Http::fake(['https://whatsapp.example/send' => Http::response([], 200)]);
        config([
            'whatsapp.driver' => 'http',
            'whatsapp.endpoint' => 'https://whatsapp.example/send',
            'whatsapp.token' => 'secret-token',
            'whatsapp.sender' => 'Barasira',
        ]);

        app(WhatsAppService::class)->send('+223 70 00 00 00', 'Votre notification');

        Http::assertSent(fn ($request) => $request->url() === 'https://whatsapp.example/send'
            && $request->hasHeader('Authorization', 'Bearer secret-token')
            && $request['to'] === '+22370000000'
            && $request['message'] === 'Votre notification'
            && $request['sender'] === 'Barasira');
    }
}
