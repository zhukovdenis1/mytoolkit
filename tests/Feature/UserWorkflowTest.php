<?php

namespace Tests\Feature;

use Illuminate\Testing\Fluent\AssertableJson;

class UserWorkflowTest extends BaseIntegrationTest
{
    protected function setUp(): void
    {
        parent::setUp();

        // Создаём тестового пользователя
        /*\App\Models\User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->authenticateUser();*/
    }

    public function test_user_can_access_protected_endpoint(): void
    {

        $response = $this->getJson('/api/me', $this->withAuthHeaders());

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => 2,
                'email' => 'test2@example.com',
            ]);
    }

    public function test_user_can_create_a_post(): void
    {
        $data = [
            'title' => 'Test Note',
            'text' => 'This is a test note', // Используем 'text' вместо 'content'
            'categories' => [],
        ];

        $response = $this->postJson(route('notes.store'), [
            'title' => 'New Post',
            'content' => 'This is a test post.',
        ], $this->withAuthHeaders());

        $response->assertStatus(422)
            ->assertJsonStructure(['message', 'errors'=>['text']]);

        $response = $this->postJson(route('notes.store'), [
            "title" => "Note 1",
            "text" => "Note text",
            "categories" => []
        ], $this->withAuthHeaders());

        //dd($response->getContent(), now()->format('Y-m-d H:i:s'));

        $response->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('data.title', 'Note 1')
                ->where('data.categories', [])
                ->whereType('data.created_at', 'string')
                ->where('data.created_at', fn ($date) =>
                    \Carbon\Carbon::hasFormat($date, 'Y-m-d\TH:i:s.u\Z') &&
                    now()->diffInSeconds(\Carbon\Carbon::parse($date)) <= 1
                )
                ->where('data.updated_at', fn ($date) =>
                    \Carbon\Carbon::hasFormat($date, 'Y-m-d\TH:i:s.u\Z') &&
                    now()->diffInSeconds(\Carbon\Carbon::parse($date)) <= 10
                )
            );
    }
}
