<?php

declare(strict_types=1);

namespace App\Modules\Note\Http\Resources\User;

use App\Http\Resources\BaseResource;


class NoteFileResource extends BaseResource
{
    public function toArray($request): array
    {
        $data = parent::toArray($request);
        return $data;
    }
}
