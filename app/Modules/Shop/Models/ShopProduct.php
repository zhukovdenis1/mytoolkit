<?php

namespace App\Modules\Shop\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;


class ShopProduct extends BaseModel
{
    use HasFactory;
    //use SoftDeletes;

    protected $table = 'shop_products';

    protected $connection = 'mysql_shop';

    protected $fillable = [
        'id',
        'id_ae',
        'source',
        'vk_category',
        'epn_category_id',
        'category_id',
        'category_0',
        'category_1',
        'category_2',
        'category_3',
        'title_ae',
        'title_source',
        'title',
        'search_ae',
        'hru',
        'price',
        'price_from',
        'price_to',
        'description',
        'characteristics',
        'reviews',
        'photo',
        'video',
        'vk_attachment',
        'rating',
        'epn_month_income',
        'epn_cashback',
        'info',
        'not_found_at',
        'posted_at',
        'updated_at',
        'deleted_at',
        'reviews_updated_at',
        'reviews_amount',
        'articles_created_at',
        'extra_data'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'not_found_at',
        'posted_at',
        'reviews_updated_at',
    ];

    protected $casts = [
        'photo' => 'array',
        'reviews' => 'array',
        'video' => 'array',
        'info' => 'array',
        'extra_data' => 'array',
    ];

//    public static function filter($page = 1, $category = 0, $search = '')
//    {
//        $limit = 48;
//        $offset = $limit * ($page - 1);
//
//
//        $query = ShopProduct::query()
//            ->select('id', 'title', 'title_ae', 'photo', 'rating', 'price', 'price_from', 'price_to', 'hru')
//            ->whereNull('deleted_at')
//            ->whereNull('not_found_at')
//            ->whereNotIn('category_0', [16002,1309])
//            ->orderBy('id', 'desc')
//            ->offset($offset)
//            ->limit($limit);
//
//        if ($search) {
//            $query->where('title', 'like', "%{$search}%");
//        }
//
//        if ($category) {
//            //$query->where('category_id', $category);
//
//            $query->where(function (Builder $query) use ($category) {
//                $query->orWhere('category_id', $category)
//                      ->orWhere('category_0', $category)
//                      ->orWhere('category_1', $category)
//                      ->orWhere('category_2', $category)
//                      ->orWhere('category_3', $category);
//            });
//        }
//
//        return $query->get();
//    }

    public static function saveProduct(array $data)
    {
        return static::create([
            'id_ae' => $data['id_ae'] ?? null,
            'source' => $queueItem['source'] ?? null,
            'vk_category' => $queueItem['category'] ?? null,
            'epn_category_id' => $queueItem['info']['attributes']['goodsCategoryId'] ?? null,
            'category_id' => $data['category_id'] ?? null,
            'category_0' => $data['category_0'] ?? null,
            'category_1' => $data['category_1'] ?? null,
            'category_2' => $data['category_2'] ?? null,
            'category_3' => $data['category_3'] ?? null,
            'title_ae' => $data['title_ae'] ?? null,
            'title_source' => $data['title_source'] ?? null,
            'title' => $data['title'] ?? null,
            'hru' => $data['hru'] ?? null,
            'price' => $data['price'] ?? 0,
            'price_from' => $data['price_from'] ?? 0,
            'price_to' => $data['price_to'] ?? 0,
            'description' => $data['description'] ?? null,
            'characteristics' => $data['characteristics'] ?? null,
            'reviews' => $data['reviews'] ?? null,
            'photo' => $data['photo'] ?? null,
            'video' => $data['video'] ?? null,
            'vk_attachment' => $queueItem['info']['vk_attachment'] ?? null,
            'rating' => $data['rating'] ?? 0,
            'info' => $queueItem['info'] ?? null,
        ]);
    }
}
