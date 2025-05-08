<?php

declare(strict_types=1);

namespace App\Modules\ShopArticle\Models;

use App\Models\BaseModel;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id Автоинкрементный ID (целое число)
 * @property string $title Заголовок заметки (строка)
 * @property array|null $text Текст заметки (массив в JSON, может быть null)
 * @property Carbon $created_at Время создания записи (дата и время)
 * @property Carbon $updated_at Время обновления записи (дата и время)
 *
 */
class ShopArticle extends BaseModel
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'title',
        'keywords',
        'description',
        'name',
        'h1',
        'uri',
        'code',
        'text',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $casts = [
        'id' => 'integer',
        'text' => 'array',//string
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
