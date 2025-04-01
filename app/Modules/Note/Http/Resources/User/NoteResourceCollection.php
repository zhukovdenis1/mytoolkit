<?php

namespace App\Modules\Note\Http\Resources\User;

use App\Http\Resources\BaseResourceCollection;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use JsonSerializable;

class NoteResourceCollection extends BaseResourceCollection
{
    public static $wrap = 'notes';

//    /**
//     * Transform the resource collection into an array.
//     *
//     * @param  Request  $request
//     * @return array|Arrayable|JsonSerializable
//     */
//    public function toArray(Request $request): array|JsonSerializable|Arrayable
//    {
//        return parent::toArray($request);
//    }

    public function paginationInformation($request, $paginated, $default)
    {
        return [
            'meta' => [
                'current_page' => $paginated['current_page'],
                'per_page' => $paginated['per_page'],
                'total' => $paginated['total']
            ]
        ];
    }
}
