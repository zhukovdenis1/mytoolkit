<?php

namespace App\Modules\Shop\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class ShopCategory extends BaseModel
{
//    use HasFactory;
//
//    protected $table = 'ali_category';
//
//    protected $connection = 'mysql_ali';

    use HasFactory;

    protected $table = 'shop_categories';

    //protected $connection = 'mysql_shop';

    protected $primaryKey = 'id_ae';

    protected $fillable = [
        'id_ae',
        'parent_id',
        'level',
        'hidden',
        'title',
        'hru',
        'parents'
    ];

}
