<?php

declare(strict_types=1);

namespace App\Helpers;

class DeepSeekHelper
{
    private string $apiKey;
    private string $apiUrl = 'https://api.deepseek.com/v1/';

    public function __construct()
    {
        $this->apiKey = config('deepseek.key');
    }

    public function chat($message/*, $model = 'deepseek-chat'*/): array
    {
        //$model = 'deepseek-chat';
        $model = 'deepseek-reasoner';
        $data = [
            'model' => $model,
            'messages' => [['role' => 'user', 'content' => $message]],
        ];

        return $this->sendRequest('chat/completions', $data);
    }

    private function sendRequest(string $endpoint, array $data): array
    {
        $ch = curl_init($this->apiUrl . $endpoint);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->apiKey
            ]
        ]);
        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response, true);
    }
}
