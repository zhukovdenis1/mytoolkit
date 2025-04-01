<?php

namespace App\Models;

use App\DTOs\DTO;
use App\DTOs\DTOInterface;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
//    public function dto(array $extraData = []): DTO
//    {
//        return DTO::fromArray(array_merge($this->toArray(), $extraData));
//    }
}
