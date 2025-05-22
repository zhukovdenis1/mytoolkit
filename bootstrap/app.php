<?php

use App\Exceptions\ExceptionHandler;
use App\Http\Middleware\HandleCors;
use App\Http\Middleware\JsonUnescapeUnicode;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\DebugMode;

$domain = $_SERVER['HTTP_HOST'] ?? '';

if ($domain === 'deshevyi.loc' || $domain === 'deshevyi.ru') {
    return Application::configure(basePath: dirname(__DIR__))
        ->withRouting(
            web: __DIR__ . '/../routes/web_shop.php',
            api: __DIR__.'/../routes/api_shop.php',
            commands: __DIR__.'/../routes/console.php',
            health: '/up',
        )
        ->withMiddleware(function (Middleware $middleware) {
            $middleware->alias([
                'shop_visits' => \App\Http\Middleware\RegisterVisit::class,
            ]);
            //$middleware->append(JwtMiddleware::class);
            $middleware->append(DebugMode::class);
            $middleware->append(JsonUnescapeUnicode::class);
            $middleware->append(HandleCors::class);
            //$middleware->append(\App\Http\Middleware\RegisterVisit::class); - перенесено в routes
        })
        ->withExceptions(new ExceptionHandler()/*function (Exceptions $exceptions) {
        //
    }*/)->create();
} else {
    return Application::configure(basePath: dirname(__DIR__))
        ->withRouting(
            web: __DIR__.'/../routes/web.php',
            api: __DIR__.'/../routes/api.php',
            commands: __DIR__.'/../routes/console.php',
            health: '/up',
        )
        ->withMiddleware(function (Middleware $middleware) {
            //$middleware->append(JwtMiddleware::class);
            $middleware->append(\App\Http\Middleware\MyIp::class);
            $middleware->append(DebugMode::class);
            $middleware->append(JsonUnescapeUnicode::class);
            $middleware->append(HandleCors::class);
        })
        ->withExceptions(new ExceptionHandler()/*function (Exceptions $exceptions) {
        //
    }*/)->create();
}


