<?php

declare(strict_types=1);

namespace App\Modules\Note\DTOs;

use Carbon\Carbon;

final class CreateNoteDTO
{
    public function __construct(
        public readonly int $id,
        public readonly int $userId,
        public readonly string $title,
        public readonly ?string $text,
        public readonly array $categories,
        public readonly Carbon $createdAt,
        public readonly Carbon $updatedAt,

    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'],
            $data['user_id'],
            $data['title'],
            $data['text'],
            $data['categories'] ?? [],
            Carbon::parse($data['created_at']),
            Carbon::parse($data['updated_at']),

        );
    }
}
