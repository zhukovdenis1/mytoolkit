<?php
/*
namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Routing\Router;

class RouteServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->configureMiddleware();

        $this->routes(function () {
            $this->mapRoutesForCurrentSite();
            $this->mapConsoleRoutes();
            $this->mapHealthRoutes();
        });
    }

    protected function configureMiddleware()
    {
        $router = app(Router::class);
        $domain = request()->getHost();

        // Общие middleware для всех сайтов
        $router->pushMiddlewareToGroup('web', \App\Http\Middleware\DebugMode::class);
        $router->pushMiddlewareToGroup('web', \App\Http\Middleware\JsonUnescapeUnicode::class);

        // Условные middleware
        if ($this->isShopDomain($domain)) {
            $router->aliasMiddleware('shop_visits', \App\Http\Middleware\RegisterVisit::class);
        } else {
            $router->pushMiddlewareToGroup('web', \App\Http\Middleware\HandleCors::class);
            $router->pushMiddlewareToGroup('web',\App\Http\Middleware\MyIp::class);
        }
    }

    protected function mapRoutesForCurrentSite()
    {
        if ($this->isShopDomain(request()->getHost())) {
            $this->mapShopRoutes();
        } else {
            $this->mapMainRoutes();
        }
    }

    protected function isShopDomain($domain): bool
    {
        //return $domain == env('APP_SHOP_URL');
        return in_array($domain, ['deshevyi.loc', 'deshevyi.ru']);
    }

    protected function mapShopRoutes()
    {
        Route::middleware('web')
            ->group(base_path('routes/web_shop.php'));

        Route::prefix('api')
            ->middleware('api')
            ->group(base_path('routes/api_shop.php'));
    }

    protected function mapMainRoutes()
    {
        Route::middleware('web')
            ->group(base_path('routes/web.php'));

        Route::prefix('api')
            ->middleware('api')
            ->group(base_path('routes/api.php'));
    }

    protected function mapConsoleRoutes()
    {
        require base_path('routes/console.php');
    }

    protected function mapHealthRoutes()
    {
        Route::get('/up', function () {
            return 'OK';
        });
    }
}*/
