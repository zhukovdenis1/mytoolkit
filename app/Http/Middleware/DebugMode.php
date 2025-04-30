<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DebugMode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Получаем список разрешенных IP из .env
        $debugIps = explode(',', env('DEBUG_IP_LIST', ''));
        $debugMode = env('APP_DEBUG', false);

        // Если IP не в списке - отключаем debug режим
        if (!$debugMode && in_array($request->ip(), $debugIps)) {
            config(['app.debug' => true]);
        }
        //var_dump(app()->hasDebugModeEnabled());die;
        return $next($request);
    }
}
