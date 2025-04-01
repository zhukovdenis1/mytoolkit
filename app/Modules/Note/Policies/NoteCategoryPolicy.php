<?php

namespace App\Modules\Note\Policies;

use App\Models\User;
use App\Modules\Note\Models\NoteCategory;

class NoteCategoryPolicy
{
    public function show(User $user, NoteCategory $category): bool
    {
        return $user->id === $category->user_id;
    }

    public function update(User $user, NoteCategory $category): bool
    {
        return $user->id === $category->user_id;
    }

    public function destroy(User $user, NoteCategory $category): bool
    {
        return $user->id === $category->user_id;
    }

}
