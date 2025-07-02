<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AutoBreadcrumbsMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Не нужно явно вызывать Breadcrumbs::before() здесь
        // Просто пропускаем для определенных маршрутов
        if (!in_array($request->path(), ['login', 'register'])) {
            // Активируем автоматические breadcrumbs через макрос
            //Request::macro('autoBreadcrumbs', true);
            //$request->attributes->set('auto_breadcrumbs', true);
        }

        return $next($request);
    }
}
