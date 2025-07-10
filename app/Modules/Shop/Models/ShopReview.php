<?php

declare(strict_types=1);

namespace App\Modules\Shop\Models;

use App\Models\BaseModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id Автоинкрементный ID
 * @property string $id_ae Внешний ID отзывa
 * @property int $product_id ID товара
 * @property Carbon|null $date Дата отзыва
 * @property int|null $grade Оценка (1-5)
 * @property string $text Текст отзыва
 * @property array|null $reviewer Данные автора
 * @property array|null $images Изображения отзыва
 * @property int $likesAmount Количество лайков
 * @property int $sort Порядок сортировки
 * @property array|null $additional Дополнительные данные
 * @property array|null $raw Сырые данные отзыва
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property-read ShopProduct $product Связанный товар
 */
class ShopReview extends BaseModel
{
    use HasFactory;

    protected $table = 'shop_reviews';

    //protected $connection = 'mysql_shop';

    protected $fillable = [
        'id_ae',
        'product_id',
        'date',
        'grade',
        'text',
        'reviewer',
        'images',
        'likesAmount',
        'sort',
        'additional',
        'raw',

    ];

    protected $casts = [
        'id' => 'integer',
        'product_id' => 'integer',
        'date' => 'date',
        'grade' => 'integer',
        'reviewer' => 'array',
        'images' => 'array',
        'likesAmount' => 'integer',
        'sort' => 'integer',
        'additional' => 'array',
        'raw' => 'array',
    ];

    /**
     * Связь с товаром
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(ShopProduct::class);
    }

//    /**
//     * Преобразует дату в нужный формат при сериализации
//     */
//    public function getDateAttribute($value): ?string
//    {
//        return $value ? Carbon::parse($value)->toDateTimeString() : null;
//    }
}
