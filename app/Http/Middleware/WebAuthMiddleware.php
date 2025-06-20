<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Modules\Auth\Models\Token;

class WebAuthMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        //$refreshToken = $request->cookie('refresh_token');
        $refreshToken = $_COOKIE['refresh_token'] ?? null;

        if ($refreshToken) {
            $token = Token::with('user')
                ->where('refresh_token', $refreshToken)
                ->first();

            if ($token?->user) {
                auth()->setUser($token->user);
            }
        }

        return $next($request);
    }
}
