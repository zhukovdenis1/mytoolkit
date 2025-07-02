<?php

namespace Tests\Unit\Services;

use App\Modules\Note\DTOs\User\CreateNoteRequestDTO;
use App\Modules\Note\Models\Note;
use App\Modules\Note\Models\NoteCategory;
use PHPUnit\Framework\TestCase;

class NoteServiceTest extends TestCase
{
    public function _testCreateNote(): void
    {
        // Мокируем модель Note
        $noteMock = $this->getMockBuilder(Note::class)
            ->disableOriginalConstructor() // Не запускаем конструктор
            ->onlyMethods(['save', 'categories']) // Мокируем нужные методы
            ->getMock();

        // Настройка мока: что произойдет при вызове метода save
        $noteMock->method('save')->willReturn(true);

        // Мокируем метод categories
        $categoryMock = $this->createMock(NoteCategory::class);
        $noteMock->method('categories')->willReturn($categoryMock);

        // Настройка мока для sync на объекте отношения
        $categoryMock->expects($this->once()) // Ожидаем, что метод sync будет вызван один раз
        ->method('sync')
            ->with([1, 2]) // Проверяем, что sync будет вызван с правильными данными
            ->willReturnSelf(); // Возвращаем сам объект отношения (для цепочки вызовов)

        // DTO для входных данных
        $dto = new CreateNoteRequestDTO(
            title: 'Test Note',
            text: 'This is a test note.',
            userId: 1,
            categories: [1, 2]
        );

        // Сервис с моком модели
        $noteService = new \App\Modules\Note\Services\User\NoteService();

        // Вызов метода createNote
        $response = $noteService->createNote($dto);

        // Проверка: результаты должны соответствовать ожидаемым
        $this->assertEquals('Test Note', $response->title);
        $this->assertEquals('This is a test note.', $response->text);
        $this->assertEquals([1, 2], $response->categories);
    }
}
