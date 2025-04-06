<?php

namespace App\Modules\Shop\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class ShopCategory extends BaseModel
{
    use HasFactory;

    protected $table = 'ali_category';

    protected $connection = 'mysql_shop';
}
