<?php

namespace App\Http\Middleware;

use App\Models\Firewall;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FirewallMiddleware
{
    /**
     * Проверяет IP и URI на угрозы.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->ip();
        $uri = $request->getRequestUri();

        $firewallEntry = Firewall::where('ip', $ip)->first();

        $counter = $firewallEntry->counter ?? 0;

        if ($counter > 2) {
            // Увеличиваем счетчик и блокируем запрос
            $firewallEntry->incrementCounter();
            return response('Access denied', 403);
        }


        //$dangerousPatterns = ['.env', 'wp-content', '.php'];
        $danger = false;
        $dangerousPatterns = ['.env', 'wp-content', 'wp-admin'];
        foreach ($dangerousPatterns as $pattern) {
            if (str_contains($uri, $pattern)) {
                $danger = true;
            }
        }

        if ($danger) {
            if ($firewallEntry) {
                $firewallEntry->incrementCounter();
            } else {
                // Добавляем IP в черный список
                Firewall::create([
                    'ip' => $ip,
                    'uri' => $uri,
                    'counter' => 1
                ]);
            }
        }

        return $next($request);
    }
}
