<?php

declare(strict_types=1);

namespace App\Modules\FileStorage\Models;

use App\Models\BaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


/**
 * @property int $id Автоинкрементный ID (целое число)
 * @property int $user_id ID пользователя (целое число)
 * @property string $name Имя файла (строка)
 * @property string $ext Расширение файла (строка)
 * @property string $type Тип файла (enum: image, video, audio, text)
 * @property string $mime_type
 * @property int $module_id ID модуля (целое число)
 * @property string $module_name Имя модуля (enum: note, calendar, word)
 * @property int $size Размер файла (целое число)
 * @property int $storage_id ID хранилища (целое число)
 * @property string|null $private_hash Хэш для приватных файлов (строка, может быть null)
 * @property int $request_counter Счетчик запросов (целое число)
 * @property Carbon|null $requested_at Время последнего запроса (дата и время, может быть null)
 * @property Carbon $created_at Время создания записи (дата и время)
 * @property Carbon $updated_at Время обновления записи (дата и время)
 * @property array|null $data Дополнительные данные (массив, может быть null)
 *
 * @property-read User $user Пользователь, связанный с файлом
 */
class File extends BaseModel
{
    use HasFactory;

    protected $hidden = ['private_hash'];



    protected $fillable = [
        'user_id',
        'name',
        'ext',
        'type',
        'mime_type',
        'module_id',
        'module_name',
        'size',
        'storage_id',
        'private_hash',
        'request_counter',
        'requested_at',
        'cached_until',
        'data',
    ];

    protected $dates = ['created_at', 'updated_at','requested_at'];

    protected $casts = [
        'id' => 'integer', // Автоинкрементный ID (целое число)
        'user_id' => 'integer', // Поле `user_id` (целое число)
        'name' => 'string', // Поле `name` (строка)
        'ext' => 'string', // Поле `ext` (строка)
        'type' => 'string', // enum ['image', 'video', 'audio', 'text']
        'mime_type' => 'string', // enum ['image', 'video', 'audio', 'text']
        'module_id' => 'integer', // Поле `module_id` (целое число)
        'module_name' => 'string', // enum ['note', 'calendar', 'word']
        'size' => 'integer', // Поле `size` (целое число)
        'storage_id' => 'integer', // Поле `storage_id` (целое число)
        'private_hash' => 'string', // Поле `private_hash` (строка)
        'request_counter' => 'integer', // Поле `request_counter` (целое число)
        'requested_at' => 'datetime', // Поле `requested_at` (дата и время)
        'cached_until' => 'datetime',
        'created_at' => 'datetime', // Поле `created_at` (дата и время)
        'updated_at' => 'datetime', // Поле `updated_at` (дата и время)
        'data' => 'array', // Поле `data` (json, преобразуется в массив)
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function storage(): BelongsTo
    {
        return $this->belongsTo(Storage::class);
    }
}
