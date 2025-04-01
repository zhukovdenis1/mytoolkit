<?php

namespace App\Modules\Note\Models;

use App\Models\BaseModel;

/**
 * Промежуточная модель для связи многие-ко-многим между Note и NoteCategory
 *
 * @property int $note_id ID заметки
 * @property int $note_category_id ID категории
 *
 * @property-read Note $note Связанная заметка
 * @property-read NoteCategory $category Связанная категория
 */
class NoteNoteCategory extends BaseModel
{
    protected $table = 'note_note_category';

    protected $fillable = [
        'note_id',
        'note_category_id',
    ];

    protected $casts = [
        'note_id' => 'integer',
        'note_category_id' => 'integer',
    ];

    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = null;

    // Связь с моделью Note (если требуется)
    public function note()
    {
        return $this->belongsTo(Note::class, 'note_id');
    }

    // Связь с моделью NoteCategory (если требуется)
    public function category()
    {
        return $this->belongsTo(NoteCategory::class, 'note_category_id');
    }
}
