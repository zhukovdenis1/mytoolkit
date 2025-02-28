<?php

namespace App\Modules\Note\Http\Policies;

use App\Models\User;
use App\Modules\Note\Models\NoteCategory;

class NoteCategoryPolicy
{
    public function rud(User $user, NoteCategory $category): bool
    {
        return $category->user_id === $user->id;
    }
}
