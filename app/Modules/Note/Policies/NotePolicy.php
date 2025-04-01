<?php

namespace App\Modules\Note\Policies;

use App\Models\User;
use App\Modules\Note\Models\Note;

class NotePolicy
{

    public function show(User $user, Note $note): bool
    {
        return $user->id === $note->user_id;
    }

    public function update(User $user, Note $note): bool
    {
        return $user->id === $note->user_id;
    }

    public function destroy(User $user, Note $note): bool
    {
        return $user->id === $note->user_id;
    }

    public function storeFile(User $user, Note $note): bool
    {
        return $user->id === $note->user_id;
    }
}
