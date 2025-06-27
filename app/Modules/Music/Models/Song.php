<?php

declare(strict_types=1);

namespace App\Modules\Music\Models;

use App\Models\BaseModel;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id Автоинкрементный ID (целое число)
 * @property int $user_id ID пользователя (целое число)
 * @property int|null $singer_id ID исполнителя (целое число, может быть null)
 * @property string $keys Ключи (строка)
 * @property string $key_orig Оригинальный ключ (строка)
 * @property float $speed Скорость (число с плавающей точкой)
 * @property string $title Название песни (строка)
 * @property string $text Текст песни (строка)
 * @property Carbon|null $published_at Время публикации (дата и время, может быть null)
 * @property Carbon $created_at Время создания записи (дата и время)
 * @property Carbon $updated_at Время обновления записи (дата и время)
 *
 * @property-read User $user Пользователь, создавший песню
 * @property-read SongSinger|null $singer Исполнитель песни
 */
class Song extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'singer_id',
        'keys',
        'key_orig',
        'speed',
        'title',
        'text',
        'published_at',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'published_at',
    ];

    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'singer_id' => 'integer',
        'speed' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'published_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function singer(): BelongsTo
    {
        return $this->belongsTo(SongSinger::class, 'singer_id');
    }
}
