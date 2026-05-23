<?php
// ── Añadir a config/services.php ──────────────────────────────────
return [
    // ... (mantener lo que ya existe) ...

    'api' => [
        'base_url' => env('API_BASE_URL', 'http://localhost:5201/api'),
        'timeout'  => env('API_TIMEOUT', 30),
    ],

    'google' => [
        'client_id'     => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect'      => env('GOOGLE_REDIRECT_URI'),
    ],
];
