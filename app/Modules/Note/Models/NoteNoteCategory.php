<?php

namespace App\Modules\Note\Models;

use App\Models\BaseModel;

class NoteNoteCategory extends BaseModel
{
    protected $table = 'note_note_category';

    protected $fillable = [
        'note_id',
        'note_category_id',
    ];

    public $timestamps = false;

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
