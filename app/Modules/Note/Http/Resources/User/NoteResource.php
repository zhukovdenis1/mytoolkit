<?php

declare(strict_types=1);

namespace App\Modules\Note\Http\Resources\User;

use App\Http\Resources\BaseResource;


class NoteResource extends BaseResource
{
    public static $wrap = 'note';
    public function toArray($request): array
    {
        $data = parent::toArray($request);
        $data['categories'] = $this->categories->pluck('id')->toArray();

        return $data;
    }
}
