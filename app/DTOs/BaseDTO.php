<?php

namespace App\DTOs;

class BaseDTO implements DTOInterface
{
    public static function fromArray(array $data): static
    {
        $dto = new static();
        foreach ($data as $key => $value) {
            $dto->$key = $value;
        }

        return $dto;
    }

    public function toArray(): array
    {
        return (array) $this;
    }
}
