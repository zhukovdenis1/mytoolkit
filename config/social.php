<?php


return [
    'vk' => [
        'key' => env('VK_KEY'),
        'post_group' => [
            'token' => env('VK_POST_GROUP_TOKEN', ''),
            'id' => env('VK_POST_GROUP_ID', '')
        ]
    ],
    'telegram' => [
        'post_chat' => [
            'token' => env('TELEGRAM_POST_CHAT_TOKEN', ''),
            'id' => env('TELEGRAM_POST_CHAT_ID', '')
        ]
    ]
];
