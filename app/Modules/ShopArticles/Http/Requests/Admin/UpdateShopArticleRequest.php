<?php

declare(strict_types=1);

namespace App\Modules\ShopArticles\Http\Requests\Admin;

use App\Http\Requests\BaseFormRequest;

class UpdateShopArticleRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'title' => 'nullable|string|max:255',
            'text' => 'nullable|string',
        ];
    }
}
