<?php

namespace Tests\Unit\Http\Controllers\User;

use App\Modules\Note\DTOs\User\CreateNoteResponseDTO;
use App\Modules\Note\Http\Controllers\User\NoteController;
use App\Modules\Note\Http\Requests\User\StoreNoteRequest;
use App\Modules\Note\Http\Resources\User\NoteResource;
use App\Modules\Note\Services\User\NoteService;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\TestCase;

class NoteControllerTest extends TestCase
{
    use WithFaker;

    public function test_store_creates_note_successfully()
    {
        $data = [
            'title' => 'Test Note',
            'text' => 'Test content of the note',
            'categories' => [1, 2],
        ];

        // Мокаем HTTP-запрос
        $request = $this->createMock(StoreNoteRequest::class);
        $request->expects($this->once())
            ->method('user')
            ->willReturn((object)['id' => 1]);
        $request->expects($this->exactly(3))
            ->method('input')
            ->willReturnMap([
                ['title', null, $data['title']],
                ['text', null, $data['text']],
                ['categories', null, $data['categories']],
            ]);

        // Создаем реальный экземпляр DTO для ответа
        $responseDTO = new CreateNoteResponseDTO(
            id: 1,
            title: $data['title'],
            text: $data['text'],
            categories: $data['categories'],
            createdAt: now(),
            updatedAt: now(),
        );

        // Мокаем сервис
        $noteService = $this->createMock(NoteService::class);
        $noteService->expects($this->once())
            ->method('createNote')
            ->with($this->callback(function ($dto) use ($data) {
                return $dto->title === $data['title']
                    && $dto->text === $data['text']
                    && $dto->categories === $data['categories'];
            }))
            ->willReturn($responseDTO);

        // Создаем контроллер и вызываем метод
        $controller = new NoteController($noteService);
        $response = $controller->store($request);

        // Проверяем ответ
        $this->assertInstanceOf(NoteResource::class, $response);
        $this->assertEquals($responseDTO->title, $response->title);
    }
}
