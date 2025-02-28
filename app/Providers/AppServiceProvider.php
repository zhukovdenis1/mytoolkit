<?php

namespace App\Providers;


use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Response;

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
        // Переопределяем макрос toJson для всех ресурсов, чтобы применить глобально JSON_UNESCAPED_UNICODE
        JsonResource::macro('toJson', function ($options = 0) {
            return json_encode($this->resolve(), JSON_UNESCAPED_UNICODE | $options);
        });

        // Для коллекций ресурсов
        ResourceCollection::macro('toJson', function ($options = 0) {
            return json_encode($this->resolve(), JSON_UNESCAPED_UNICODE | $options);
        });

        // Переопределение макроса для response()->json
        Response::macro('json', function ($value = null, $status = 200, array $headers = [], $options = 0) {
            // Применяем глобально JSON_UNESCAPED_UNICODE
            return response()->json($value, $status, $headers, $options | JSON_UNESCAPED_UNICODE);
        });

        $this->loadMigrationsFrom(base_path('app/Modules/Note/Database/Migrations'));
    }
}
