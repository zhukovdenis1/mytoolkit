<?php

declare(strict_types=1);

namespace App\Modules\ShopArticles\Http\Requests\Admin;

use App\Http\Requests\BaseFormRequest;

class UpdateContentShopArticleRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'text' => 'nullable|string',
        ];
    }
}
