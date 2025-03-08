<?php

declare(strict_types=1);

namespace App\Modules\FileStore\Services;

class FileStoreService
{
    private $dir = '';
    public function saveFile(array $validatedData): array
    {
        return [
            'note' => [],
            'success' => $note->exists
        ];
    }
}
