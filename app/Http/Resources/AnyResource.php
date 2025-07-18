<?php

namespace App\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AnyResource extends BaseResource
{

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->resource
        ];
    }
}
