<?php

namespace App\Http\Middleware;

use AllowDynamicProperties;
use App\Models\MyIp as MyIpModel;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;


class MyIp
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    /*public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request); // Сначала пропускаем запрос

        $userId = $request->user()?->id ?? null;

        if ($userId == 1001) {
            MyIpModel::upsert(
                [['ip' => $request->ip(), 'created_at' => now(), 'updated_at' => now()]],
                ['ip'],
                ['updated_at']
            );
        }

        return $response;
    }*/

    public function terminate(Request $request, $response): void
    {
        $userId = $request->user()?->id ?? null;

        if ($userId == 1001) {
            MyIpModel::upsert(
                [['ip' => $request->ip(), 'created_at' => now(), 'updated_at' => now()]],
                ['ip'],
                ['updated_at']
            );
        }
    }


}
