<?php
namespace App\Modules\Shop\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Storage;

class EpnApiClient
{
    private const BASE_URL = 'https://app.epn.bz/';
    private const AUTH_URL = 'https://oauth2.epn.bz/';
    private const TOKENS_FILE = 'epn/tokens.json';

    private $client;
    private $clientId;
    private $clientSecret;
    private $checkIp;

    private $accessToken;
    private $refreshToken;
    private $ssidToken;

    public function __construct(string $clientId, string $clientSecret, bool $checkIp = false)
    {
        $this->client = new Client();
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->checkIp = $checkIp;

        $this->loadTokens();
    }

    /**
     * Загружает токены из хранилища
     */
    private function loadTokens(): void
    {
        if (Storage::exists(self::TOKENS_FILE)) {
            $data = json_decode(Storage::get(self::TOKENS_FILE), true);

            if (json_last_error() === JSON_ERROR_NONE) {
                $this->accessToken = $data['access_token'] ?? null;
                $this->refreshToken = $data['refresh_token'] ?? null;

                if ($this->accessToken && $this->isTokenExpired($this->accessToken)) {
                    $this->accessToken = null;
                }
            }
        }
    }

    /**
     * Проверяет истек ли срок действия JWT токена
     */
    private function isTokenExpired(string $token): bool
    {
        try {
            $parts = explode('.', $token);
            if (count($parts) !== 3) return true;

            $payload = json_decode(base64_decode($parts[1]), true);
            return isset($payload['exp']) && $payload['exp'] < time();
        } catch (\Exception $e) {
            return true;
        }
    }

    /**
     * Сохраняет токены в хранилище
     */
    private function saveTokens(): void
    {
        $data = [
            'access_token' => $this->accessToken,
            'refresh_token' => $this->refreshToken,
            'updated_at' => now()->toDateTimeString(),
        ];

        Storage::put(self::TOKENS_FILE, json_encode($data, JSON_PRETTY_PRINT));
    }

    /**
     * Очищает сохраненные токены
     */
    public function clearTokens(): void
    {
        if (Storage::exists(self::TOKENS_FILE)) {
            Storage::delete(self::TOKENS_FILE);
        }
        $this->accessToken = null;
        $this->refreshToken = null;
    }


    /**
     * Выполняет GET запрос к API
     */
    public function get(string $endpoint, array $params = []): array
    {
        return $this->makeRequest('GET', $endpoint, $params);
    }

    /**
     * Выполняет POST запрос к API
     */
    public function post(string $endpoint, array $data = []): array
    {
        return $this->makeRequest('POST', $endpoint, [], $data);
    }

    /**
     * Основной метод для выполнения запросов с обработкой ошибок авторизации
     */
    private function makeRequest(string $method, string $endpoint, array $params = [], array $data = []): array
    {
        // Если нет токенов - проходим авторизацию
        if (empty($this->accessToken)) {
            $this->authenticate();
        }

        $attempts = 0;
        $maxAttempts = 2; // Максимум 2 попытки (1 обычная + 1 после обновления токена)

        while ($attempts < $maxAttempts) {
            try {
                $options = [
                    'headers' => [
                        'X-ACCESS-TOKEN' => $this->accessToken,
                    ],
                ];

                if (!empty($params)) {
                    $options['query'] = $params;
                }

                if (!empty($data)) {
                    $options['json'] = $data;
                }

                $response = $this->client->request($method, self::BASE_URL . $endpoint, $options);
                return json_decode($response->getBody()->getContents(), true);
            } catch (GuzzleException $e) {
                if ($e->getCode() === 401) {
                    // Если 401 ошибка - пробуем обновить токен
                    $this->refreshToken();
                    $attempts++;
                    continue;
                }

                throw $e;
            }
        }

        // Если после обновления токена снова 401 - пробуем полную авторизацию
        $this->authenticate();

        try {
            $options = [
                'headers' => [
                    'X-ACCESS-TOKEN' => $this->accessToken,
                ],
            ];

            if (!empty($params)) {
                $options['query'] = $params;
            }

            if (!empty($data)) {
                $options['json'] = $data;
            }

            $response = $this->client->request($method, self::BASE_URL . $endpoint, $options);
            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            throw new \RuntimeException('Failed to make API request after authentication attempts: ' . $e->getMessage());
        }
    }

    /**
     * Полная авторизация (получение SSID + получение токенов)
     */
    private function authenticate(): void
    {
        $this->getSsidToken();
        $this->getAccessToken();
    }

    /**
     * Получает SSID токен
     */
    private function getSsidToken(): void
    {
        try {
            $response = $this->client->get(self::AUTH_URL . 'ssid', [
                'query' => [
                    'v' => 2,
                    'client_id' => $this->clientId,
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            if (empty($data['result']) || empty($data['data']['attributes']['ssid_token'])) {
                throw new \RuntimeException('Failed to get SSID token');
            }

            $this->ssidToken = $data['data']['attributes']['ssid_token'];
        } catch (GuzzleException $e) {
            throw new \RuntimeException('SSID token request failed: ' . $e->getMessage());
        }
    }

    /**
     * Получает access и refresh токены
     */
    private function getAccessToken(): void
    {
        try {
            $response = $this->client->post(self::AUTH_URL . 'token', [
                'query' => ['v' => 2],
                'json' => [
                    'ssid_token' => $this->ssidToken,
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'grant_type' => 'client_credential',
                    'check_ip' => $this->checkIp,
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            if (empty($data['result']) || empty($data['data']['attributes']['access_token'])) {
                throw new \RuntimeException('Failed to get access token');
            }

            $this->accessToken = $data['data']['attributes']['access_token'];
            $this->refreshToken = $data['data']['attributes']['refresh_token'];

            // Сохраняем токены
            $this->saveTokens();
        } catch (GuzzleException $e) {
            throw new \RuntimeException('Access token request failed: ' . $e->getMessage());
        }
    }

    /**
     * Обновляет access токен с помощью refresh токена
     */
    private function refreshToken(): void
    {
        if (empty($this->refreshToken)) {
            $this->authenticate();
            return;
        }

        try {
            $response = $this->client->post(self::AUTH_URL . 'token/refresh', [
                'json' => [
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $this->refreshToken,
                    'client_id' => $this->clientId,
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            if (empty($data['result'])) {
                throw new \RuntimeException('Failed to refresh token');
            }

            $this->accessToken = $data['data']['attributes']['access_token'];
            $this->refreshToken = $data['data']['attributes']['refresh_token'];

            // Сохраняем новые токены
            $this->saveTokens();
        } catch (GuzzleException $e) {
            // Если refresh не сработал - пробуем полную авторизацию
            $this->authenticate();
        }
    }
}
