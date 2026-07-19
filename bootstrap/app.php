<?php

use App\Http\Middleware\AddSecurityHeaders;
use App\Http\Middleware\EnsureUserHasRole;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => EnsureUserHasRole::class,
        ]);

        // Sin proxy inverso delante (Cloudflare, balanceador...) no hay que confiar en
        // las cabeceras X-Forwarded-*: cualquiera podría falsificar su IP con ellas.
        // No llamamos aquí a trustProxies(at:) porque en este punto del arranque
        // env()/config() todavía no están listos; la lista de proxies de confianza
        // se define en config/trustedproxy.php (TRUSTED_PROXIES en .env), que Laravel
        // lee dentro del propio middleware, ya en tiempo de petición.

        $middleware->web(append: [AddSecurityHeaders::class]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
