<?php

namespace App\Http\Controllers;

use App\Models\BaseModel;
use Illuminate\Http\JsonResponse;

abstract class BaseController
{
    protected function abortWrongUser(BaseModel $model)
    {
        \abortWrongUser($model->user_id);
    }

    /**
     * Success response method.
     *
     * @param array $result
     * @param int $code
     * @return JsonResponse
     */
    protected function jsonResponse(array $result = [], int $code = 200): JsonResponse
    {
        return response()->json($result, $code);
    }
}
