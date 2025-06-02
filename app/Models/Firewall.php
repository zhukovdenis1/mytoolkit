<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

/**
 * @property int $id Автоинкрементный ID
 * @property string $ip IP-адрес
 * @property string $uri URI запроса
 * @property int $counter Счётчик запросов
 * @property Carbon $created_at Время создания записи
 * @property Carbon $updated_at Время обновления записи
 */
class Firewall extends Model
{
    protected $table = 'firewall';

    protected $fillable = [
        'ip',
        'uri',
        'counter',
        'blocked_until',
        'user_agent'
    ];

    protected $casts = [
        'id' => 'integer',
        'counter' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'blocked_until' => 'datetime',
    ];

    /**
     * Увеличивает счётчик запросов
     */
//    public function incrementCounter(): void
//    {
//        $this->counter++;
//        $this->save();
//    }
}
