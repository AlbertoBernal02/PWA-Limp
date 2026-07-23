<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // La cuota gratuita de Gemini es POR PROYECTO (por API key), no por usuario:
        // verificado empíricamente que "generate_content_free_tier_requests" tiene
        // límite de 15 peticiones/minuto compartidas entre TODOS los usuarios de la
        // app a la vez. Por eso este límite es global (sin ->by()), no por usuario.
        // Cada mensaje del chat puede disparar más de una llamada a Gemini (el paso
        // intermedio de function calling), así que se deja margen amplio por debajo
        // de esas 15 peticiones/minuto reales.
        RateLimiter::for('chat-ia', function (Request $request) {
            return [
                Limit::perMinute(5),
                Limit::perDay(150),
            ];
        });
    }
}
