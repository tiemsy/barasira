<?php

return [
    'plans' => [
        'week' => ['duration_days' => 7, 'price' => 50000],
        'month' => ['duration_days' => 30, 'price' => 150000],
        'quarter' => ['duration_days' => 90, 'price' => 350000],
    ],
    'categories' => ['services', 'commerce', 'finance', 'education', 'technology', 'events', 'ngo', 'other'],
    'recipient' => env('PARTNER_REQUEST_EMAIL', env('MAIL_CONTACT_ADDRESS', 'contact@barasira.com')),
];
