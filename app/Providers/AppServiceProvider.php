<?php

namespace App\Providers;


use App\Http\Resources\SimpleResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Blade;
use Illuminate\Pagination\Paginator;

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
//        // Переопределяем макрос toJson для всех ресурсов, чтобы применить глобально JSON_UNESCAPED_UNICODE
//        JsonResource::macro('toJson', function ($options = 0) {
//            return json_encode($this->resolve(), JSON_UNESCAPED_UNICODE | $options);
//        });
//
//        // Для коллекций ресурсов
//        ResourceCollection::macro('toJson', function ($options = 0) {
//            return json_encode($this->resolve(), JSON_UNESCAPED_UNICODE | $options);
//        });
//
//        // Переопределение макроса для response()->json
//        Response::macro('json', function ($value = null, $status = 200, array $headers = [], $options = 0) {
//            // Применяем глобально JSON_UNESCAPED_UNICODE
//            return response()->json($value, $status, $headers, $options | JSON_UNESCAPED_UNICODE);
//        });

        $this->loadMigrationsFrom(base_path('app/Modules/Note/Database/Migrations'));
        $this->loadMigrationsFrom(base_path('app/Modules/FileStorage/Database/Migrations'));
        $this->loadMigrationsFrom(base_path('app/Modules/Shop/Database/Migrations'));
        $this->loadMigrationsFrom(base_path('app/Modules/ShopArticles/Database/Migrations'));

        $this->loadViewsFrom(app_path('Modules/Shop/Resources/views'), 'Shop');
        // Регистрируем компоненты модуля Shop
        Blade::anonymousComponentNamespace(
            'Shop::components', // Путь относительно зарегистрированного views
            'shop' // Префикс для компонентов (<x-shop::... />)
        );

        // Для всех моделей в директории app/Models
//        foreach (glob(app_path('Models/*.php')) as $modelFile) {
//            $model = 'App\\Models\\'.basename($modelFile, '.php');
//            $policy = 'App\\Policies\\'.basename($modelFile, '.php').'Policy';
//
//            if (class_exists($policy)) {
//                Gate::policy($model, $policy);
//            }
//        }

//        Gate::policies([
//            Comment::class => CommentPolicy::class,
//            User::class => UserPolicy::class,
//        ]);

        //SimpleResource::withoutWrapping();

        Paginator::defaultView('pagination::default');
    }
}
