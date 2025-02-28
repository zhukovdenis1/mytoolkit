<?php

declare(strict_types=1);

namespace App\Modules\Note\DTOs;

use Carbon\Carbon;

final class NoteDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly ?string $text,
        public readonly array $categories,
        public readonly Carbon $createdAt,
        public readonly Carbon $updatedAt,
        public readonly int $userId,
    ) {}
}

/*class NoteDTO
{
    public int $id;
    public string $title;
    public string $text;
    public Carbon $created_at;
    public Carbon $updated_at;
    public int $user_id; // Добавляем поле user_id
    //public array $categories;

    public function __construct(
        int $id,
        string $title,
        string $text,
        Carbon $created_at,
        Carbon $updated_at,
        int $user_id,
        //array $categories

    ) {
        $this->id = $id;
        $this->title = $title;
        $this->text = $text;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
        $this->user_id = $user_id; // Присваиваем user_id
        //$this->categories = $categories;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'],
            $data['title'],
            $data['text'],
            Carbon::parse($data['created_at']),
            Carbon::parse($data['updated_at']),
            $data['user_id'],
            //$data['categories'] ?? []
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'text' => $this->text,
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
            'user_id' => $this->user_id, // Добавляем user_id
            //'categories' => $this->categories
        ];
    }
}*/
