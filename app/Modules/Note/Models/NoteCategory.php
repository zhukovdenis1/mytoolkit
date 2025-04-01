<?php

declare(strict_types=1);

namespace App\Modules\Note\Models;

use App\Models\BaseModel;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id Автоинкрементный ID категории (целое число)
 * @property int $user_id ID пользователя-владельца (целое число)
 * @property int|null $parent_id ID родительской категории (целое число, может быть null)
 * @property string $name Название категории (строка)
 * @property Carbon $created_at Время создания записи (дата и время)
 * @property Carbon $updated_at Время обновления записи (дата и время)
 *
 * @property-read User $user Пользователь-владелец категории
 * @property-read NoteCategory|null $parent Родительская категория
 * @property-read \Illuminate\Database\Eloquent\Collection<NoteCategory> $children Дочерние категории
 * @property-read \Illuminate\Database\Eloquent\Collection<Note> $notes Заметки в этой категории
 */
class NoteCategory extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'name',
        'parent_id',
        'user_id',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $hidden = [
        'pivot',
        'user_id',
    ];

    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'parent_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function children(): HasMany
    {
        return $this->hasMany(
            self::class,
            'parent_id'
        );
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(
            self::class,
            'parent_id'
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function notes(): BelongsToMany
    {
        return $this->belongsToMany(
            Note::class,
            'note_note_category'
        );
    }
}
