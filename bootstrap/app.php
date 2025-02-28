<?php

use App\Exceptions\ExceptionHandler;
use App\Http\Middleware\JsonUnescapeUnicode;
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
    ->withMiddleware(function (Middleware $middleware) {
        //$middleware->append(JwtMiddleware::class);
        $middleware->append(JsonUnescapeUnicode::class);

        $middleware->append(\App\Http\Middleware\HandleCors::class);
    })
    ->withExceptions(new ExceptionHandler()/*function (Exceptions $exceptions) {
        //
    }*/)->create();
