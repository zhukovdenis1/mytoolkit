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
            'h1' => 'required|string|max:255',
            'title' => 'nullable|string|max:255',
            'keywords' => 'nullable|string|max:16000',
            'description' => 'nullable|string|max:16000',
            'text' => 'nullable|string|max:16000',
            'note' => 'nullable|string|max:16000',
            'code' => 'nullable|string|max:255',
            'separation' => 'nullable|string|max:255',
            'site_id' => 'nullable|integer',
            'product_id' => 'nullable|integer',
            'published_at' => 'nullable|date_format:Y-m-d\TH:i:s.v\Z,Y-m-d,Y-m-d H:i:s,d.m.Y,d.m.Y H:i:s',
        ];
    }
}
