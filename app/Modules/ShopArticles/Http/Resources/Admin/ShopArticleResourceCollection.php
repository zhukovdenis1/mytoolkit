<?php

namespace App\Modules\ShopArticles\Http\Resources\Admin;

use App\Http\Resources\BaseResourceCollection;

class ShopArticleResourceCollection extends BaseResourceCollection
{
    public static $wrap = 'articles';

    public function paginationInformation($request, $paginated, $default)
    {
        return [
            'meta' => [
                'current_page' => $paginated['current_page'],
                'per_page' => $paginated['per_page'],
                'total' => $paginated['total']
            ]
        ];
    }
}
