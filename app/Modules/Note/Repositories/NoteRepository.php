<?php

namespace App\Modules\Note\Repositories;

use App\Modules\Note\Models\Note;

class NoteRepository
{
    public function save(Note $note): Note
    {
        $note->save();

        return $note;
    }
}
