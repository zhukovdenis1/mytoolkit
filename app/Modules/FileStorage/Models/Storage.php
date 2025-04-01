<?php

declare(strict_types=1);

namespace App\Modules\FileStorage\Models;

use App\Models\BaseModel;
use App\Models\User;
use App\Modules\FileStorage\Models\Enums\StorageType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id Автоинкрементный ID (целое число)
 * @property int $user_id ID пользователя (целое число)
 * @property int $backup_id
 * @property string $type Тип файла (enum)
 * @property Carbon $created_at Время создания записи (дата и время)
 * @property Carbon $updated_at Время обновления записи (дата и время)
 * @property array|null $data Дополнительные данные (массив, может быть null)
 *
 * @property-read User $user Пользователь, связанный с файлом
 */
class Storage extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'backup_id',
        'type',
        'data',
    ];

    protected $dates = ['created_at', 'updated_at'];

    protected $casts = [
        'id' => 'integer',
        'backup_id' => 'integer',
        'user_id' => 'integer',
        'type' => StorageType::class,
        'created_at' => 'datetime', // Поле `created_at` (дата и время)
        'updated_at' => 'datetime', // Поле `updated_at` (дата и время)
        'data' => 'array', // Поле `data` (json, преобразуется в массив)
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function getTypes(): array
    {
        return StorageType::values();
    }

    public function backup(): BelongsTo
    {
        return $this->belongsTo(Storage::class);
    }
}
