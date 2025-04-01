<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Configuration\Exceptions as BaseExceptions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
//use Sentry\Laravel\Integration;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class ExceptionHandler
{
    protected int $jsonFlags = JSON_UNESCAPED_UNICODE;//JSON_UNESCAPED_SLASHES ^ JSON_UNESCAPED_UNICODE;

    public function __invoke(BaseExceptions $exceptions): BaseExceptions
    {
        $this->registerHandlers($exceptions);
        // $this->registerSentryReporting($exceptions);

        return $exceptions;
    }

    protected function registerHandlers(BaseExceptions $exceptions): void
    {
        $this->renderUnauthenticated($exceptions);
        $this->renderUnauthorized($exceptions);
        $this->renderNotFound($exceptions);
        $this->renderCustomError($exceptions);
        $this->renderValidationErrors($exceptions);
        //$this->renderHttpResponseErrors($exceptions);
    }


    protected function renderValidationErrors(BaseExceptions $exceptions): void
    {
        $exceptions->renderable(
            fn (ValidationException $e, ?Request $request = null) => $this->response(
                message: __('Validation Failed'),
                code: 422,
                asJson: $request?->expectsJson() ?? false,
                errors: $e->validator->errors()
            )
        );

//
//        $exceptions->renderable(function (ValidationException $e) {
//            // Если это наш кастомный HttpResponseException в disguise
//            if ($e->getMessage() === 'The given data was invalid.' &&
//                $e->response instanceof \Illuminate\Http\JsonResponse) {
//                return $e->response;
//            }
//
//            return new JsonResponse([
//                'message' => __('Validation Failed'),
//                'errors' => $e->errors(),
//            ], Response::HTTP_UNPROCESSABLE_ENTITY);
//        });
    }

//    protected function renderHttpResponseErrors(BaseExceptions $exceptions): void
//    {
//        $exceptions->renderable(function (HttpResponseException $e, Request $request = null) {
//            $response = $e->getResponse();
//
//            if ($response instanceof JsonResponse) {
//                return $response;
//            }
//
//            return $this->buildResponse(
//                message: $response->getContent() ?: __('Unprocessable Entity'),
//                code: $response->getStatusCode(),
//                isJson: $request?->expectsJson() ?? false
//            );
//        });
//    }

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

    protected function renderCustomError(BaseExceptions $exceptions): void
    {
        $exceptions->renderable(
            function (ErrorException $e, ?Request $request = null) {
                $errors = new MessageBag();
                $errors->add('custom', __($e->getMessage()));
                return $this->response(
                    message: 'Custom error',
                    code: 409,
                    asJson: $request?->expectsJson() ?? false,
                    errors: $errors,
                );
            }
        );
    }

//    protected function reportSentry(BaseExceptions $exceptions): void
//    {
//        $exceptions->reportable(
//            fn (Throwable $e) => Integration::captureUnhandledException($e)
//        );
//    }

    protected function response(string $message, int $code, bool $asJson, $errors = null): Response
    {
        if ($asJson) {
            return response()->json(['message' => $message, 'errors' => $errors], $code, options: $this->jsonFlags);
        }

        $this->registerErrorViewPaths();

        return response()->view($this->view($code), [
            'errors' => $errors
        ]);
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
