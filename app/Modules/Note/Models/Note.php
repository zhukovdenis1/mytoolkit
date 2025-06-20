<?php

declare(strict_types=1);

namespace App\Modules\Note\Models;

use App\Models\BaseModel;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id Автоинкрементный ID (целое число)
 * @property int|null $parent_id ID родительской заметки (целое число, может быть null)
 * @property int $user_id ID пользователя (целое число)
 * @property string $title Заголовок заметки (строка)
 * @property array|null $text Текст заметки (массив в JSON, может быть null)
 * @property Carbon $created_at Время создания записи (дата и время)
 * @property Carbon $updated_at Время обновления записи (дата и время)
 * @property Carbon $published_at Время публикации (дата и время)
 *
 * @property-read User $user Пользователь, создавший заметку
 * @property-read Note|null $parent Родительская заметка
 * @property-read \Illuminate\Database\Eloquent\Collection<NoteCategory> $categories Категории заметки
 */
class Note extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'title',
        'text',
        'user_id',
        'parent_id',
        'published_at',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'published_at',
    ];

    protected $hidden = [
        'user_id',
    ];

    protected $casts = [
        'id' => 'integer',
        'parent_id' => 'integer',
        'user_id' => 'integer',
        'text' => 'string',//array
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'published_at' => 'datetime',
    ];

//    public function getTextAttribute($value)
//    {
//        return is_string($value) ? json_decode($value, true) : $value;
//    }
//
//    public function setTextAttribute($value)
//    {
//        $this->attributes['text'] = is_string($value) ? $value : json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
//    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(
            NoteCategory::class,
            'note_note_category'
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }
}
