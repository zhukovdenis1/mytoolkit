<?php

declare(strict_types=1);

namespace App\Modules\Note\DTOs\User;

use Carbon\Carbon;

class NoteCategoryDTO
{
    public function __construct(
        public readonly int $id,
        public readonly ?int $parentId,
        public readonly string $name,
        public readonly Carbon $createdAt,
        public readonly Carbon $updatedAt,
    ) {}

    /*public static function fromArray(array $data): self
    {
        return new self(
            $data['id'],
            $data['parent_id'] ?? null,
            $data['name'],
            Carbon::parse($data['created_at']),
            Carbon::parse($data['updated_at'])
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'parent_id' => $this->parentId,
            'name' => $this->name,
            'created_at' => $this->createdAt->toIso8601String(),
            'updated_at' => $this->updatedAt->toIso8601String(),
        ];
    }*/
}
