<?php

namespace Tests\Feature\Note\User;

use App\Modules\Note\Models\Note;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class StoreNoteTest extends TestCase
{
    use RefreshDatabase;

    public function _test_store_creates_note_successfully()
    {
        // Создаем пользователя
        $user = User::factory()->create();

        // Генерируем JWT-токен для пользователя
        $token = JWTAuth::fromUser($user);

        // Данные для новой заметки
        $data = [
            'title' => 'Test Note',
            'text' => 'This is a test note', // Используем 'text' вместо 'content'
            'categories' => [],
        ];

        // Отправляем запрос с токеном
        $response = $this->postJson(route('notes.store'), $data, [
            'Authorization' => 'Bearer ' . $token
        ]);

        // Проверяем, что статус ответа 201 (успешно создано)
        $response->assertStatus(200);

        // Проверяем, что заметка появилась в базе данных
        $this->assertDatabaseHas('notes', [
            'title' => 'Test Note',
            'text' => 'This is a test note',
            'user_id' => $user->id,
        ]);
    }

    public function _test_store_fails_validation()
    {
        // Создаем пользователя
        $user = User::factory()->create();

        // Генерируем JWT-токен для пользователя
        $token = JWTAuth::fromUser($user);

        // Данные с пустым заголовком (невалидные)
        $data = [
            'title' => '',
            'text' => 'This is a test note',
            'categories' => [],
        ];

        // Отправляем запрос с токеном
        $response = $this->postJson(route('notes.store'), $data, [
            'Authorization' => 'Bearer ' . $token
        ]);

        // Проверяем, что статус ошибки валидации
        $response->assertStatus(422);

        // Проверяем, что ошибка касается поля 'title'
        $response->assertJsonValidationErrors(['title']);
    }

    public function _test_store_requires_authentication()
    {
        // Данные для новой заметки
        $data = [
            'title' => 'Test Note',
            'text' => 'This is a test note',
            'categories' => [],
        ];

        // Отправляем запрос без токена
        $response = $this->postJson(route('notes.store'), $data);

        // Проверяем, что требуется авторизация
        $response->assertStatus(401);
    }

    public function _test_store_attaches_correct_user_id()
    {
        // Создаем пользователя
        $user = User::factory()->create();

        // Генерируем JWT-токен для пользователя
        $token = JWTAuth::fromUser($user);

        // Данные для новой заметки
        $data = [
            'title' => 'Test Note',
            'text' => 'This is a test note',
            'categories' => [],
        ];

        // Отправляем запрос с токеном
        $response = $this->postJson(route('notes.store'), $data, [
            'Authorization' => 'Bearer ' . $token
        ]);

        // Проверяем, что заметка привязана к текущему пользователю
        $this->assertDatabaseHas('notes', [
            'title' => 'Test Note',
            'text' => 'This is a test note',
            'user_id' => $user->id,
        ]);
    }

    private function test_store_logs_creation_event()
    {
        // Мокаем логирование
        Log::shouldReceive('info')
            ->once()
            ->withArgs(function ($message, $context) {
                return $message === 'Note created' && isset($context['title']) && $context['title'] === 'Test Note';
            });

        // Создаем пользователя
        $user = User::factory()->create();

        // Генерируем JWT-токен для пользователя
        $token = JWTAuth::fromUser($user);

        // Данные для новой заметки
        $data = [
            'title' => 'Test Note',
            'text' => 'This is a test note',
        ];

        // Отправляем запрос с токеном
        $this->postJson(route('notes.store'), $data, [
            'Authorization' => 'Bearer ' . $token
        ]);
    }
}
