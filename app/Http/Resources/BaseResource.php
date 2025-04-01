<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\JsonResponse;

abstract class BaseResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    /*public function toArray(Request $request): array
    {
        return json_decode(json_encode(parent::toArray($request), JSON_UNESCAPED_UNICODE), true);
        /*return [
            'id' => $this->id ?? null,
            'created_at' => isset($this->created_at) ? $this->created_at?->format('d.m.Y, H:i:s') : null,
            'updated_at' => isset($this->updated_at) ? $this->updated_at?->format('d.m.Y, H:i:s') : null,
        ];
    }*/

//    public function toResponse($request): JsonResponse
//    {
//        return response()->json(
//            //$this->additional + ['data' => $this->resolve($request)], // Убираем обертку `data`
//            $this->resolve($request),
//            200,
//            [],
//            JSON_UNESCAPED_UNICODE
//        );
//
//    }

    public function with(Request $request)
    {
        return [
            'api' => true,
        ];
    }

    public function withTimeStamps(array $data, Request $request): array
    {
        return array_merge($data, [
            'createdAt' => isset($this->created_at) ? $this->created_at?->format('d.m.Y H:i:s') : null,
            'updatedAt' => isset($this->updated_at) ? $this->updated_at?->format('d.m.Y H:i:s') : null,
        ]);
    }
}

