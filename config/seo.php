<?php

return [
    'name' => env('SEO_SITE_NAME', 'Barasira'),
    'title' => env('SEO_DEFAULT_TITLE', 'Barasira — Trouvez un prestataire fiable au Mali'),
    'description' => env(
        'SEO_DEFAULT_DESCRIPTION',
        'Trouvez rapidement des prestataires qualifiés au Mali pour vos travaux, services à domicile et besoins professionnels.'
    ),
    'image' => env('SEO_DEFAULT_IMAGE', '/images/logo-barasira.png'),
    'country' => env('SEO_COUNTRY', 'Mali'),
];
