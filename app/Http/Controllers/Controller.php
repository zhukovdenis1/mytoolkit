<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

abstract class Controller
{
    use AuthorizesRequests;

    /**
     * Success response method.
     *
     * @param array $result
     * @param int $code
     * @return JsonResponse
     */
    protected function response(array $result = [], int $code = 200): JsonResponse
    {
        return response()->json($result, $code);
    }

    /**
     * Error response method.
     *
     * @param array $errorMessages
     * @param int $code
     * @return JsonResponse
     */
    protected function errorResponse(array|string $errorMessages = 'error', int $code = 404): JsonResponse
    {
        return response()->json(['error' => $errorMessages], $code);
    }
}
