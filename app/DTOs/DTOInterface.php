<?php

namespace App\DTOs;

interface DTOInterface
{
    public function toArray(): array;

    public static function fromArray(array $data): static;
}
