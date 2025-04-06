<?php

namespace App\Modules\Shop\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class ShopProduct extends BaseModel
{
    use HasFactory;

    protected $table = 'ali_product';

    protected $connection = 'mysql_shop';

    protected $casts = [
        'photo' => 'array',
        'reviews' => 'array',
        'video' => 'array',
    ];

    public static function filter($page = 1, $category = 0, $search = '')
    {
        $limit = 48;
        $offset = $limit * ($page - 1);


        $query = ShopProduct::query()
            ->select('id', 'title', 'title_ae', 'photo', 'rating', 'price', 'price_from', 'price_to', 'hru')
            ->where('del', 0)
            ->where('moderated', 1)
            ->whereNotIn('category_0', [16002,1309])
            ->orderBy('id', 'desc')
            ->offset($offset)
            ->limit($limit);

        if ($search) {
            $query->where('title', 'like', "%{$search}%");
        }

        if ($category) {
            //$query->where('category_id', $category);

            $query->where(function (Builder $query) use ($category) {
                $query->orWhere('category_id', $category)
                      ->orWhere('category_0', $category)
                      ->orWhere('category_1', $category)
                      ->orWhere('category_2', $category)
                      ->orWhere('category_3', $category);
            });
        }

        return $query->get();
    }

    /*public static function getRules(): array
    {
        return [
            'page'     => ['nullable', 'integer', 'min:1', 'max:100'],
            'category' => ['nullable', 'integer'],
            'search'   => ['nullable', 'string', 'max:50'],
        ];
    }*/
}
