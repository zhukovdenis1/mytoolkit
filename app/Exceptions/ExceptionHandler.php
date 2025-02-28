<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Configuration\Exceptions as BaseExceptions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
//use Sentry\Laravel\Integration;
use Illuminate\Validation\UnauthorizedException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class ExceptionHandler
{
    protected int $jsonFlags = JSON_UNESCAPED_UNICODE;//JSON_UNESCAPED_SLASHES ^ JSON_UNESCAPED_UNICODE;

    public function __invoke(BaseExceptions $exceptions): BaseExceptions
    {
        $this->renderUnauthenticated($exceptions);
        $this->renderUnauthorized($exceptions);
        $this->renderNotFound($exceptions);

        //$this->reportSentry($exceptions);

        return $exceptions;
    }

    protected function renderUnauthenticated(BaseExceptions $exceptions): void
    {
        $exceptions->renderable(
            fn (AuthenticationException $e, ?Request $request = null) => $this->response(
                message: __('Unauthenticated*'),
                code: 401,
                asJson: $request?->expectsJson() ?? false
            )
        );
    }

    protected function renderUnauthorized(BaseExceptions $exceptions): void
    {
        $exceptions->renderable(
            fn (AccessDeniedHttpException $e, ?Request $request = null) => $this->response(
                message: __('Unauthorized*'),
                code: 401,
                asJson: $request?->expectsJson() ?? false
            )
        );
    }

    protected function renderNotFound(BaseExceptions $exceptions): void
    {
        $exceptions->renderable(
            fn (NotFoundHttpException $e, ?Request $request = null) => $this->response(
                message: __('Not Found*'),
                code: 404,
                asJson: $request?->expectsJson() ?? false
            )
        );
    }

    protected function reportSentry(BaseExceptions $exceptions): void
    {
        $exceptions->reportable(
            fn (Throwable $e) => Integration::captureUnhandledException($e)
        );
    }

    protected function response(string $message, int $code, bool $asJson): Response
    {
        if ($asJson) {
            return response()->json(compact('message'), $code, options: $this->jsonFlags);
        }

        $this->registerErrorViewPaths();

        return response()->view($this->view($code), status: $code);
    }

    protected function view(int $code): string
    {
        return view()->exists('errors::' . $code) ? 'errors::' . $code : 'errors::400';
    }

    protected function registerErrorViewPaths(): void
    {
        View::replaceNamespace(
            'errors',
            collect(config('view.paths'))
                ->map(fn (string $path) => "$path/errors")
                ->push($this->vendorViews())
                ->all()
        );
    }

    protected function vendorViews(): string
    {
        return __DIR__ . '/../../vendor/laravel/framework/src/Illuminate/Foundation/Exceptions/views';
    }
}
