<?php

declare(strict_types=1);

namespace App\Modules\Shop\Http\Requests\Shared;

use App\Http\Requests\BaseFormRequest;

class SetParsedProductRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            //'id_queue' => 'required|integer|exists:shop_products_parse_queue,id',
            'id_queue' => 'required|integer',
            'data' => 'nullable|array',
            'brcr' => 'nullable|array',
            'error_code' => 'nullable|integer'
        ];
    }
}
