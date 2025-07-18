<?php

declare(strict_types=1);

namespace App\Modules\Shop\Models;

use App\Models\BaseModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id Автоинкрементный ID
 * @property string|null $sid Уникальный идентификатор сессии (16 символов)
 * @property string|null $ip IP-адрес посетителя (в бинарном формате)
 * @property string|null ip_address IP-адрес посетителя
 * @property string|null $user_agent User agent посетителя
 * @property string|null $referrer Реферер
 * @property int|null $tid ID сайта с которого зашли
 * @property string|null $uri URI страницы
 * @property string|null $page_name Название страницы
 * @property int|null $item_id ID связанного товара/элемента
 * @property int $visit_num Номер визита
 * @property bool $is_bot Флаг бота
 * @property bool $is_mobile Флаг мобильного устройства
 * @property bool $is_external Флаг внешнего перехода
 * @property Carbon $created_at Время создания записи
 */
class ShopVisit extends BaseModel
{
    use HasFactory;

    protected $table = 'shop_visits';
    public const UPDATED_AT = null;

    protected $fillable = [
        'sid',
        'ip',
        'ip_address',
        'user_agent',
        'referrer',
        'tid',
        'uri',
        'page_name',
        'item_id',
        'visit_num',
        'is_bot',
        'is_mobile',
        'is_external',
        'created_at'
    ];

    protected $casts = [
        'id' => 'integer',
        'item_id' => 'integer',
        'visit_num' => 'integer',
        'is_bot' => 'boolean',
        'is_mobile' => 'boolean',
        'is_external' => 'boolean',
        'created_at' => 'datetime',
    ];

    /**
     * Преобразует бинарный IP в строковый формат
     */
    public function getIpAttribute($value): ?string
    {
        return $value ? inet_ntop($value) : null;
    }

    /**
     * Преобразует строковый IP в бинарный формат перед сохранением
     */
    public function setIpAttribute($value): void
    {
        $this->attributes['ip'] = $value ? inet_pton($value) : null;
    }
}
