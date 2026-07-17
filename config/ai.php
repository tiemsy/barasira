<?php

return [
    'provider' => env('AI_PROVIDER', 'gemini'),
    'fallback_provider' => env('AI_FALLBACK_PROVIDER', 'groq'),
    'translation_provider' => env('AI_TRANSLATION_PROVIDER', env('AI_PROVIDER', 'gemini')),
    'timeout' => (int) env('AI_TIMEOUT', 45),
    'cache_ttl' => (int) env('AI_CACHE_TTL', 604800),

    'providers' => [
        'gemini' => [
            'key' => env('GEMINI_API_KEY'),
            'model' => env('GEMINI_MODEL', 'gemini-3.5-flash'),
            'base_url' => env('GEMINI_BASE_URL', 'https://generativelanguage.googleapis.com/v1beta'),
        ],
        'groq' => [
            'key' => env('GROQ_API_KEY'),
            'model' => env('GROQ_MODEL', 'llama-3.3-70b-versatile'),
            'base_url' => env('GROQ_BASE_URL', 'https://api.groq.com/openai/v1'),
        ],
        'openai' => [
            'key' => env('OPENAI_API_KEY'),
            'model' => env('OPENAI_MODEL', 'gpt-4.1-mini'),
            'base_url' => env('OPENAI_BASE_URL', 'https://api.openai.com/v1'),
        ],
    ],
];
