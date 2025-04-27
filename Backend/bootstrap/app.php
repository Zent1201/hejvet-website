<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function ($middleware) {
        // Menambahkan middleware CORS dan RoleMiddleware ke dalam pipeline
        $middleware->prepend(\App\Http\Middleware\CorsMiddleware::class); // Menambahkan CORS di awal
        // $middleware->append(\App\Http\Middleware\RoleMiddleware::class); // Menambahkan RoleMiddleware di akhir
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();

