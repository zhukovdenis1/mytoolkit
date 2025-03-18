<?php

declare(strict_types=1);

namespace App\Modules\Patient\Http\Resources;

use App\Http\Resources\BaseResource;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;


class PatientListResource extends BaseResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    //public static $wrap = 'patients';

    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable
     */
    public function toArray(Request $request): array|Arrayable
    {
        $data = parent::toArray($request);

        return array_map(function ($item) {
            return [
                'name' => $item['first_name'] . ' ' . $item['last_name'],
                'age' => $item['age'] . ' ' . $item['age_type'],
                'birthdate' => (new Carbon($item['birthdate']))->format('d.m.Y'),
            ];
        }, $data);
    }
}
