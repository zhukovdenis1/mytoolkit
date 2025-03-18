<?php

return [
    'cache_ttl' => env('PATIENT_CACHE_TTL', 300), // 5 минут
    'list_cache_ttl' => env('PATIENT_LIST_CACHE_TTL', 300), // 5 минут
    'list_cache_limit' => env('PATIENT_LIST_CACHE_LIMIT', 10000), // Кешируем только 10000 записей
];
