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
        'vk_post_id',
        'vk_group_id',
        'blocked_until',
        'parsed_at',
        'error_code',
        'version',
        'fix',
    ];

    protected $casts = [
        'info' => 'array',
        'created_at' => 'datetime',
        'parsed_at' => 'datetime',
        'blocked_until' => 'datetime',
    ];

}
