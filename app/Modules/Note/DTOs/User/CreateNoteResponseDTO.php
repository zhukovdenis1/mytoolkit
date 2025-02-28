<?php

namespace App\Modules\Note\DTOs\User;

use Carbon\Carbon;

class CreateNoteResponseDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly ?string $text,
        public readonly array $categories,
        public readonly Carbon $createdAt,
        public readonly Carbon $updatedAt,
    ) {}
}
