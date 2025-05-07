<?php

declare(strict_types=1);

namespace App\Modules\ShopArticle\Http\Requests\Admin;

use App\Http\Requests\BaseSearchRequest;


class SearchShopArticleRequest extends BaseSearchRequest
{
    protected array $sortableFields = ['id', 'title'];
}
