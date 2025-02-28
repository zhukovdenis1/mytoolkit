<?php

if (!function_exists('abort403')) {
    function abort403(bool $condition, string $message = 'Forbidden')
    {
        if (!$condition) {
            abort(403, $message);
        }
    }
}

if (!function_exists('abortWrongUser')) {
    function abortWrongUser(int $requestUserId)
    {
        abort403($requestUserId === auth()->id());
    }
}
