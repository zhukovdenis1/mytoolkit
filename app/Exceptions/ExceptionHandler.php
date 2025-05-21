<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Configuration\Exceptions as BaseExceptions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class ExceptionHandler
{
    protected int $jsonFlags = JSON_UNESCAPED_UNICODE;


    public function __invoke(BaseExceptions $exceptions): BaseExceptions
    {
        $this->registerHandlers($exceptions);
        return $exceptions;
    }

    protected function registerHandlers(BaseExceptions $exceptions): void
    {
        // Специфичные обработчики для конкретных исключений
        $this->renderUnauthenticated($exceptions);
        $this->renderUnauthorized($exceptions);
        $this->renderNotFound($exceptions);
        $this->renderCustomError($exceptions);
        $this->renderValidationErrors($exceptions);
        $this->renderQueryErrors($exceptions);

        // Глобальный обработчик для всех исключений
        $this->renderThrowable($exceptions);
    }

    protected function renderThrowable(BaseExceptions $exceptions): void
    {
        $exceptions->renderable(function (Throwable $e, Request $request) {
            $isApiRequest = $this->isApiRequest($request);

            if ($isApiRequest) {
                return $this->buildJsonResponse(
                    message: 'Internal Server Error',
                    code: 500,
                    errors: $this->prepareErrorDetails($e)
                );
            }

            // Для web-запросов позволим Laravel обрабатывать ошибки стандартным способом
            return null;
        });
    }

    protected function renderQueryErrors(BaseExceptions $exceptions): void
    {
        $exceptions->renderable(function (QueryException $e, Request $request) {

            // Логируем ошибку в канал sql_error (Перенес в AppServiceProvider)
            Log::channel('sql_error')->error('Database Query Error', [
                'message' => $e->getMessage(),
                'sql' => $e->getSql(),
                'bindings' => $e->getBindings(),
                //'trace' => $e->getTraceAsString(),
                'url' => $request->fullUrl(),
                'ip' => $request->ip(),
            ]);

            $isApiRequest = $this->isApiRequest($request);

            if ($isApiRequest) {
                return $this->buildJsonResponse(
                    message: 'Database Error',
                    code: 500,
                    errors: $this->prepareErrorDetails($e)
                );
            }

            return null;
        });
    }

    protected function renderValidationErrors(BaseExceptions $exceptions): void
    {
        $exceptions->renderable(function (ValidationException $e, Request $request) {
            $isApiRequest = $this->isApiRequest($request);
             if ($isApiRequest) {
                $errors = $e->validator->errors();
                if (is_object($errors)) {$errors = $errors->toArray();}
                return $this->buildJsonResponse(
                    message: 'Validation Failed',
                    code: 422,
                    errors: $errors
                );
            }

            return null;
        });
    }

    protected function renderUnauthenticated(BaseExceptions $exceptions): void
    {
        $exceptions->renderable(function (AuthenticationException $e, Request $request) {
            $isApiRequest = $this->isApiRequest($request);

            if ($isApiRequest) {
                return $this->buildJsonResponse(
                    message: 'Unauthenticated',
                    code: 401
                );
            }

            return null;
        });
    }

    protected function renderUnauthorized(BaseExceptions $exceptions): void
    {
        $exceptions->renderable(function (AccessDeniedHttpException $e, Request $request) {
            $isApiRequest = $this->isApiRequest($request);

            if ($isApiRequest) {
                return $this->buildJsonResponse(
                    message: 'Unauthorized',
                    code: 403
                );
            }

            return null;
        });
    }

    protected function renderNotFound(BaseExceptions $exceptions): void
    {
        $exceptions->renderable(function (NotFoundHttpException $e, Request $request) {
            $isApiRequest = $this->isApiRequest($request);

            if ($isApiRequest) {
                return $this->buildJsonResponse(
                    message: 'Not Found',
                    code: 404
                );
            }

            return null;
        });
    }

    protected function renderCustomError(BaseExceptions $exceptions): void
    {
        $exceptions->renderable(function (ErrorException $e, Request $request) {
            $isApiRequest = $this->isApiRequest($request);

            if ($isApiRequest) {
                return $this->buildJsonResponse(
                    message: 'Custom Error',
                    code: 409,
                    errors: ['custom' => $e->getMessage()]
                );
            }

            return null;
        });
    }

    protected function isApiRequest(?Request $request): bool
    {
        return $request && (
                $request->expectsJson() ||
                ($request->route() && in_array('api', $request->route()->middleware()))
            );
    }

    protected function prepareErrorDetails(Throwable $e): array
    {
        $details = [];

        if (app()->hasDebugModeEnabled()) {
            $details = ['message' => $e->getMessage()];
            $details['debug'] = [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => config('app.debug') ? $e->getTrace() : 'Trace is available only when APP_DEBUG=true'
            ];

            if ($e instanceof QueryException) {
                $details['debug']['sql'] = $e->getSql();
                $details['debug']['bindings'] = $e->getBindings();
            }
        }

        return $details;
    }

    protected function buildJsonResponse(string $message, int $code, array $errors = []): Response
    {
        $response = [
            'message' => $message,
            'errors' => $errors,
        ];

        return response()->json($response, $code, options: $this->jsonFlags);
    }
}
