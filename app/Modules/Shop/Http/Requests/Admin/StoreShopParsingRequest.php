<?php

declare(strict_types=1);

namespace App\Modules\Shop\Http\Requests\Admin;

use App\Http\Requests\BaseFormRequest;


class StoreShopParsingRequest extends BaseFormRequest
{

    public function rules(): array
    {
        return [
            'important' => 'nullable|boolean',
            'category_id' => 'nullable|integer',
            'data' => 'required|string',
        ];
    }
}
