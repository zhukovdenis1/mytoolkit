<?php

declare(strict_types=1);

namespace App\Modules\Shop\Models;

use App\Models\BaseModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id Автоинкрементный ID (целое число)
 * @property int $epn_id
 * @property int $pikabu_id
 * @property string $code Код купона (строка)
 * @property string $url
 * @property Carbon $date_from
 * @property Carbon $date_to
 * @property string $uri
 * @property string $title
 * @property string $description
 * @property Carbon $created_at Время создания записи (дата и время)
 * @property Carbon $updated_at Время обновления записи (дата и время)
 * @property string $info
 *
 */
class ShopCoupon extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'epn_id',
        'pikabu_id',
        'code',
        'url',
        'date_from',
        'date_to',
        'uri',
        'title',
        'description',
        'info'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'date_from',
        'date_to',
    ];

    protected $casts = [
        'id' => 'integer',
        'id_epn' => 'integer',
        'info' => 'array',
        'date_from' => 'date',
        'date_to' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public static function filter($page = 1, $search = '')
    {
        $limit = 48;
        $offset = $limit * ($page - 1);


        $query = ShopProduct::query()
            ->select('*')
            ->orderBy('id', 'desc')
            ->offset($offset)
            ->limit($limit);

        if ($search) {
            $query->where('title', 'like', "%{$search}%");
        }

        return $query->get();
    }
}
