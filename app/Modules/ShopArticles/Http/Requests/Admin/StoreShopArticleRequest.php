<?php

declare(strict_types=1);

namespace App\Modules\ShopArticles\Http\Requests\Admin;

use App\Http\Requests\BaseFormRequest;


class StoreShopArticleRequest extends BaseFormRequest
{

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'text' => 'nullable|string'
        ];
    }
}
