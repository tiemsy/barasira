<?php

return [
    'driver' => env('SMS_DRIVER', 'log'),
    'endpoint' => env('SMS_HTTP_ENDPOINT'),
    'token' => env('SMS_HTTP_TOKEN'),
    'sender' => env('SMS_SENDER', 'Barasira'),
];
