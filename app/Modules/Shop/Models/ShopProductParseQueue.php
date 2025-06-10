<?php

namespace App\Modules\Shop\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class ShopProductParseQueue extends BaseModel
{
    use HasFactory;
    protected $table = 'shop_products_parse_queue';
    public $timestamps = false;

    //protected $connection = 'mysql_shop';
    protected $fillable = [
        'source',
        'important',
        'info',
        'id_ae',
        'id_vk_post',
        'id_vk_group',
        'fix'
    ];

    protected $casts = [
        'info' => 'array',
        'created_at' => 'datetime',
        'parsed_at' => 'datetime',
        'blocked_until' => 'datetime',
    ];

}
