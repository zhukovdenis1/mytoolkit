<?php

declare(strict_types=1);

namespace App\Modules\Music\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

/**
 * @property int $id Автоинкрементный ID (целое число)
 * @property string $name Название исполнителя (строка)
 * @property Carbon $created_at Время создания записи (дата и время)
 * @property Carbon $updated_at Время обновления записи (дата и время)
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<Song> $songs Песни исполнителя
 */
class SongSinger extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function songs(): HasMany
    {
        return $this->hasMany(Song::class, 'singer_id');
    }
}
