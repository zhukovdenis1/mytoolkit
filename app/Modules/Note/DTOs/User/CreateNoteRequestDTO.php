<?php

declare(strict_types=1);

namespace App\Modules\Note\DTOs\User;

use Carbon\Carbon;

final class CreateNoteRequestDTO
{
    public function __construct(
        public readonly int $userId,
        public readonly string $title,
        public readonly ?string $text,
        public readonly array $categories=[],
    ) {}
}
