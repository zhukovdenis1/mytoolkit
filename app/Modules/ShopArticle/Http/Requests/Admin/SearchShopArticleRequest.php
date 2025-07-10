<?php

declare(strict_types=1);

namespace App\Modules\ShopArticle\Http\Requests\Admin;

use App\Http\Requests\BaseSearchRequest;


class SearchShopArticleRequest extends BaseSearchRequest
{
    protected array $sortableFields = ['id', 'title'];

    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'product_id' => 'nullable|integer',
            'site_id' => 'nullable|integer',
        ]);
    }
}
