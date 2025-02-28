<?php

namespace App\Modules\Note\Http\Resources\User;

use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;

class NoteCategoryResource extends BaseResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'noteCategory';

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
//    public function toArray(Request $request): array
//    {
//        return [
//            'id' => $this->id,
//            'parent_id' => $this->parent_id,
//            'name' => $this->name,
//        ];
//    }
}
