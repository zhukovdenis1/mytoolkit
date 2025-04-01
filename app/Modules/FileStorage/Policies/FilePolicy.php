<?php

namespace App\Modules\FileStorage\Policies;

use App\Models\User;
use App\Modules\FileStorage\Models\File;

class FilePolicy
{
//    public function update(User $user, File $file): bool
//    {
//        return $user->id === $file->user_id;
//    }

    /**
     * Проверяем право на удаление
     */
    public function delete(User $user, File $file): bool
    {
        return $user->id === $file->user_id;
    }


    public function view(User $user, File $file): bool
    {
        return empty($file->private_hash) || $user->id === $file->user_id;
    }
}
