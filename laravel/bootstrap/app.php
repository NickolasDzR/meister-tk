<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Туннель для отладки и traefik на боевом сервере отдают сайт по https,
        // а до приложения запрос доходит обычным http. Без доверия к их
        // заголовкам Laravel считает, что страница открыта по http, ставит
        // ссылки http://… — и браузер блокирует их на https-странице.
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
