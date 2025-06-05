<?php

return [
    'enabled' => env('REQUEST_STATS_ENABLED', false),

    'exclude_routes' => [
        'horizon.*',
        'telescope.*',
        'debugbar.*',
        'livewire.*',
    ],

    'exclude_uris' => [
        '/health',
        '/status',
    ],
];
