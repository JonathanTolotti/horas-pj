<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'premium'    => \App\Http\Middleware\EnsurePremium::class,
            'admin'      => \App\Http\Middleware\EnsureAdmin::class,
            'supervisor.access' => \App\Http\Middleware\EnsureSupervisorAccess::class,
            'two_factor' => \App\Http\Middleware\EnsureTwoFactorAuthenticated::class,
            'ability'    => \Laravel\Sanctum\Http\Middleware\CheckAbilities::class,
            'abilities'  => \Laravel\Sanctum\Http\Middleware\CheckForAnyAbility::class,
        ]);

        $middleware->appendToGroup('web', \App\Http\Middleware\EnsureTwoFactorAuthenticated::class);

        // Excluir webhook do CSRF
        $middleware->validateCsrfTokens(except: [
            'webhook/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->booted(function () {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    })
    ->create();
