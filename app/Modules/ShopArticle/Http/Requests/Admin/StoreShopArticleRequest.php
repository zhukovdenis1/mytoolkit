<?php

declare(strict_types=1);

namespace App\Modules\ShopArticle\Http\Requests\Admin;

use App\Http\Requests\BaseFormRequest;


class StoreShopArticleRequest extends BaseFormRequest
{

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'title' => 'nullable|string|max:255',
            'text' => 'nullable|string'
        ];
    }
}
