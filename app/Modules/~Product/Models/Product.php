<?php

namespace App\Modules\Product\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'name',
        'image',
        'price'
    ];
}
