<?php

declare(strict_types=1);

namespace App\Modules\ShopArticle\Http\Requests\Admin;

use App\Http\Requests\BaseFormRequest;

class UpdateShopArticleRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'h1' => 'required|string|max:255',
            'title' => 'nullable|string|max:255',
            'keywords' => 'nullable|string|max:16000',
            'description' => 'nullable|string|max:16000',
            'text' => 'nullable|string|max:16000',
            'code' => 'nullable|string|max:255',
            'separation' => 'nullable|string|max:255',
        ];
    }
}
