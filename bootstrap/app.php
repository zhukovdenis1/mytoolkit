<?php

use App\Exceptions\ExceptionHandler;
use App\Http\Middleware\FirewallMiddleware;
use App\Http\Middleware\HandleCors;
use App\Http\Middleware\JsonUnescapeUnicode;
use App\Http\Middleware\RequestStatsMiddleware;
use App\Scheduling\AppSchedule;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Middleware;

//use App\Http\Middleware\DebugMode;


$domain = $_SERVER['HTTP_HOST'] ?? '';
//$isShop = $domain === 'deshevyi.loc' || $domain === 'deshevyi.ru';

$siteConfig = include dirname(__FILE__) . '/../config/sites.php';

$siteData = (function($siteConfig): ?array {
    $domain = $_SERVER['HTTP_HOST'] ?? '';
    foreach ($siteConfig as $value) {
        if (in_array($domain, $value['hosts'])) {
            return $value;
        }
    }
    return null;
})($siteConfig);


if ($siteData && $siteData['group'] == 'shop') {
    $app = Application::configure(basePath: dirname(__DIR__))
        ->withRouting(
            web: __DIR__ . '/../routes/web_shop.php',
            api: __DIR__.'/../routes/api_shop.php',
            commands: __DIR__.'/../routes/console.php',
            health: '/up',
        )
        ->withMiddleware(function (Middleware $middleware) {
            $middleware->alias([
                'shop_visits' => \App\Http\Middleware\Shop\RegisterVisit::class,
            ]);
            //$middleware->append(JwtMiddleware::class);
            $middleware->append(FirewallMiddleware::class);
            $middleware->append(JsonUnescapeUnicode::class);
            $middleware->append(HandleCors::class);
            $middleware->append(RequestStatsMiddleware::class);
            //$middleware->append(\App\Http\Middleware\RegisterVisit::class); - перенесено в routes
        })
        ->withExceptions(new ExceptionHandler('shop'))
        ->create();

        //$app->macro('appId', fn() => 'shop');
} elseif ($siteData && $siteData['group'] == 'shop2') {
    $app = Application::configure(basePath: dirname(__DIR__))
        ->withRouting(
            web: __DIR__ . '/../routes/web_shop2.php',
            api: __DIR__.'/../routes/api_shop2.php',
            commands: __DIR__.'/../routes/console.php',
            health: '/up',
        )
        ->withMiddleware(function (Middleware $middleware) {
//            $middleware->alias([
//                'shop_visits' => \App\Http\Middleware\Shop\RegisterVisit::class,
//            ]);
            $middleware->append(FirewallMiddleware::class);
            $middleware->append(JsonUnescapeUnicode::class);
            $middleware->append(HandleCors::class);
            $middleware->append(RequestStatsMiddleware::class);
        })
        ->withExceptions(new ExceptionHandler('shop2'))
        ->create();

    //$app->macro('appId', fn() => 'shop');
} else {
    $app = Application::configure(basePath: dirname(__DIR__))
        ->withRouting(
            web: __DIR__.'/../routes/web.php',
            api: __DIR__.'/../routes/api.php',
            commands: __DIR__.'/../routes/console.php',
            health: '/up',
        )
        ->withMiddleware(function (Middleware $middleware) {
            //$middleware->append(JwtMiddleware::class);
            $middleware->append(\App\Http\Middleware\AutoBreadcrumbsMiddleware::class);
            $middleware->append(\App\Http\Middleware\MyIp::class);
            //$middleware->append(DebugMode::class);
            $middleware->append(JsonUnescapeUnicode::class);
            $middleware->append(HandleCors::class);
        })
        ->withExceptions(new ExceptionHandler())
//        ->configureSchedule([
//            \App\Scheduling\AppSchedule::class,
//        ])
        ->withSchedule(function (Schedule $schedule) {
            AppSchedule::handle($schedule);
        })
        ->create();

       // $app->macro('appId', fn() => 'mtk');


}

if(!$app->runningInConsole() && empty($siteData)) {
    die('Unknown site url');
}

$app->macro('siteGroup', fn() => $siteData['group'] ?? 'mtk');
$app->macro('siteId', fn() => $siteData['id'] ?? 1);


return $app;



/*
use Illuminate\Foundation\Application;
use App\Exceptions\ExceptionHandler;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withProviders()
    ->withRouting(
        health: '/up'
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Только общие middleware, которые не зависят от домена
    })
    ->withExceptions(new ExceptionHandler())
    ->create();
*/
