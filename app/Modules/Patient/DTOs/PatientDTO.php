<?php

declare(strict_types=1);

namespace App\Modules\Patient\DTOs;

use Illuminate\Contracts\Support\Arrayable;

class PatientDTO implements Arrayable
{
    public function __construct(
        public string $firstName,
        public string $lastName,
        public string $birthdate,
        public int $age,
        public string $ageType
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            $data['first_name'],
            $data['last_name'],
            $data['birthdate'],
            $data['age'],
            $data['age_type']
        );
    }

    public function toArray(): array
    {
        return [
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'birthdate' => $this->birthdate,
            'age' => $this->age,
            'age_type' => $this->ageType,
        ];
    }
}
