<?php

declare(strict_types=1);

namespace App\Modules\Patient\Http\Resources;

use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientResource extends BaseResource
{
//    public function toArray($request): array
//    {
//        return [
//            'name' => $this->first_name . ' ' . $this->last_name,
//            'birthdate' => $this->birthdate->format('d.m.Y'),
//            'age' => $this->age . ' ' . $this->age_type,
//        ];
//    }
}
