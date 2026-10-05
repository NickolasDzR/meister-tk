<?php

use App\Http\Middleware\EnsureEmailIsVerified;
use App\Http\Middleware\EnsureUserIsCompanyAdmin;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->api(prepend: [
            EnsureFrontendRequestsAreStateful::class,
        ]);

        $middleware->alias([
            'verified' => EnsureEmailIsVerified::class,
            'company.admin' => EnsureUserIsCompanyAdmin::class,
        ]);

        // Туннель для отладки и traefik на боевом сервере отдают сайт по https,
        // а до приложения запрос доходит обычным http. Без доверия к их
        // заголовкам Laravel считает, что страница открыта по http, ставит
        // ссылки http://… — и браузер блокирует их на https-странице.
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
