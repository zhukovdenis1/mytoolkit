<?php

namespace App\Modules\Shop\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class ShopVkGroupParse extends BaseModel
{
    use HasFactory;
    protected $table = 'shop_products_parse';

    protected $connection = 'mysql_shop';
}
