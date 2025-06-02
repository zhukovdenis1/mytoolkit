<?php

namespace App\Http\Middleware;

use App\Models\Firewall;
use Carbon\Carbon;
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
        $userAgent = $request->userAgent() ?? null;
        $uri = $request->getRequestUri();

        $firewallEntry = Firewall::where('ip', $ip)
            ->first();

        $counter = $firewallEntry->counter ?? 0;

        if ($firewallEntry && (new Carbon($firewallEntry->blocked_until) > Carbon::now())) {
            // Увеличиваем счетчик и блокируем запрос
            $firewallEntry->counter = $counter + 1;
            $firewallEntry->save();
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
                $firewallEntry->counter = $counter + 1;
                if ($counter > 2) {
                    $firewallEntry->blocked_until = Carbon::now()->addHours(2);
                }
                $firewallEntry->save();
            } else {
                // Добавляем IP в черный список
                Firewall::create([
                    'ip' => $ip,
                    'uri' => $uri,
                    'counter' => 1,
                    'user_agent' => $userAgent
                ]);
            }
        }

        return $next($request);
    }
}
