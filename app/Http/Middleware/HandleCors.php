<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandleCors
{
    public function handle(Request $request, Closure $next)
    {

        $response = $next($request);
//
//        $response->headers->set('Access-Control-Allow-Origin', 'https://mytoolkit.loc:3000');
//        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
//        $response->headers->set('Access-Control-Allow-Headers', 'Origin, Content-Type, Authorization');
//        $response->headers->set('Access-Control-Allow-Credentials', 'true');
//header('Access-Control-Allow-Origin: https://mytoolkit.loc:3001');
//header('Access-Control-Allow-Credentials: true');
        return $response;
    }
}
