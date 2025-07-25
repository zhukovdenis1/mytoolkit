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
 * @property int $site_id
 * @property int $product_id
 * @property string $title Заголовок заметки (строка)
 * @property string $keywords
 * @property string $description
 * @property string $name
 * @property string $h1
 * @property string $uri
 * @property string $code
 * @property string $separation
 * @property array|null $text Текст заметки (массив в JSON, может быть null)
 * @property string $note заметка (может быть null)
 * @property Carbon $created_at Время создания записи (дата и время)
 * @property Carbon $updated_at Время обновления записи (дата и время)
 * @property Carbon $published_at Время публикации записи (дата и время)
 *
 */
class ShopArticle extends BaseModel
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'site_id',
        'product_id',
        'title',
        'keywords',
        'description',
        'name',
        'h1',
        'uri',
        'code',
        'separation',
        'text',
        'note',
        'published_at'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'published_at'
    ];

    protected $casts = [
        'id' => 'integer',
        'text' => 'array',//string
//        'created_at' => 'datetime',
//        'updated_at' => 'datetime',
    ];
}
