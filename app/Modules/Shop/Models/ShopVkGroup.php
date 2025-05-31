<?php

declare(strict_types=1);

namespace App\Modules\Shop\Models;

use App\Models\BaseModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id Уникальный числовой ID (unsigned integer)
 * @property string $category Категория группы
 * @property Carbon $created_at Время создания записи
 * @property Carbon $parsed_at Время последнего парсинга
 */
class ShopVkGroup extends BaseModel
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'id',
        'category'
    ];

    protected $dates = [
        'created_at',
        'parsed_at'
    ];

    protected $casts = [
        'id' => 'integer',
        'created_at' => 'datetime',
        'parsed_at' => 'datetime',
    ];

    /**
     * Доступные категории групп
     */
    public const CATEGORIES = [
        'women' => 'Женщины',
        'men' => 'Мужчины',
        'children' => 'Дети',
        'design' => 'Дизайн',
        'auto' => 'Авто',
        'handmade' => 'Handmade',
        'fisher' => 'Рыбалка',
        'krutye-veshi' => 'Крутые вещи',
    ];
}
