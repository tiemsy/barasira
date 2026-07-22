<?php

return [
    'driver' => env('WHATSAPP_DRIVER', 'log'),
    'endpoint' => env('WHATSAPP_HTTP_ENDPOINT'),
    'token' => env('WHATSAPP_HTTP_TOKEN'),
    'sender' => env('WHATSAPP_SENDER', 'Barasira'),
];
