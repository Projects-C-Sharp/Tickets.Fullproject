<?php
// ── En bootstrap/app.php, dentro de ->withMiddleware(...) ──────────
// Agregar el alias del middleware de autenticacion de API:

/*
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'api.auth' => \App\Http\Middleware\ApiAuthenticated::class,
    ]);
})
*/
