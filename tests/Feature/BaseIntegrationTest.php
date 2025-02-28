<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BaseIntegrationTest extends TestCase
{
    //use RefreshDatabase;
    protected string $authToken;
    protected User $user;

    protected function authenticateUser(): void
    {
        /*$response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);*/

        $response = $this->postJson('/api/auth/login', [
            'email' => 'test2@example.com',
            'password' => 'password',
        ]);

        $this->authToken = $response->json('access_token');
    }

    protected function setUp(): void
    {
        parent::setUp();
        //dd(env('DB_CONNECTION'));
        // Прогон всех миграций перед тестами
        $this->artisan('migrate')->run();

        // Дополнительно можно заполнить тестовую базу данными
        $this->artisan('db:seed')->run();

        $this->user = User::factory()->create([
            'email' => 'test2@example.com',
            'password' => bcrypt('password'),
        ]);

        // Авторизация перед каждым тестом
        $this->authenticateUser();
    }

    protected function withAuthHeaders(array $headers = []): array
    {
        return array_merge($headers, [
            'Authorization' => "Bearer {$this->authToken}",
        ]);
    }
}
