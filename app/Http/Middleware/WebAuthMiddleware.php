<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Modules\Auth\Models\Token;
use Illuminate\Support\Facades\Auth;

class WebAuthMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        //$refreshToken = $request->cookie('refresh_token');
        $refreshToken = $_COOKIE['refresh_token'] ?? null;

        if (!$refreshToken) {
            //return response()->json(['error' => 'Unauthorized'], 401);
            abort(401, 'Unauthorized');
        }

        $token = Token::with('user')
            ->where('refresh_token', $refreshToken)
            ->first();

        if (!$token || !$token->user) {
            //return response()->json(['error' => 'Invalid refresh token'], 401);
            abort(401, 'Unauthorized');
        }

        // Авторизуем пользователя в системе
        //Auth::loginUsingId($token->user_id);
        //auth()->loginUsingId($token->user_id);


        auth()->setUser($token->user);

        return $next($request);
    }
}
