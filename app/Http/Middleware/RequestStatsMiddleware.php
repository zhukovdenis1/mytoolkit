<?php

namespace App\Http\Middleware;

use App\Models\RequestStat;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class RequestStatsMiddleware
{


    public function handle($request, Closure $next): Response
    {
        if (!$this->shouldCollectStats($request)) {
            return $next($request);
        }

        $request->attributes->set('request_stats', [
            'startTime' => microtime(true),
            'dbQueriesTime' => 0,
        ]);

        // Подписываемся на события запросов к БД
        DB::listen(function ($query) use ($request) {
            $stats = $request->attributes->get('request_stats');
            $stats['dbQueriesTime'] += $query->time;
            $request->attributes->set('request_stats', $stats);
        });

        // Включаем лог запросов для подсчета их количества
        DB::enableQueryLog();
        $response = $next($request);
        //$this->terminate($request, $response);
        return $response;
    }

    public function terminate(Request $request, Response $response): void
    {
        if (!$this->shouldCollectStats($request) || !$request->attributes->has('request_stats')) {
            return;
        }
        $stats = $request->attributes->get('request_stats');
        $totalTime = (microtime(true) - $stats['startTime']) * 1000;
        $dbQueriesTime = $stats['dbQueriesTime'];

        $memoryUsage = memory_get_peak_usage(true) / 1024; // в KB
        $queryCount = count(DB::getQueryLog());

        RequestStat::create([
            'total_time' => round($totalTime, 2),
            'db_time' => round($dbQueriesTime, 2),
            'query_count' => $queryCount,
            'memory_usage' => round($memoryUsage, 2),
            'route_name' => Route::currentRouteName() ?? '',
            'method' => $request->method(),
            'uri' => $request->getRequestUri(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'status_code' => $response->getStatusCode(),
        ]);

        // Отключаем лог запросов
        DB::disableQueryLog();
    }

    protected function shouldCollectStats($request): bool
    {
        if (!config('request_stats.enabled', false)) {
            return false;
        }

        // Проверка исключенных маршрутов
        $routeName = Route::currentRouteName();
        $excludedRoutes = config('request_stats.exclude_routes', []);

        foreach ($excludedRoutes as $excludedRoute) {
            if ($routeName && Str::is($excludedRoute, $routeName)) {
                return false;
            }
        }

        // Проверка исключенных URI
        $excludedUris = config('request_stats.exclude_uris', []);

        foreach ($excludedUris as $excludedUri) {
            if (Str::is($excludedUri, $request->getRequestUri())) {
                return false;
            }
        }

        return true;
    }
}
