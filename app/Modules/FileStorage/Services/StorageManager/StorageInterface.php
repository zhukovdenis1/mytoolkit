<?php

namespace App\Modules\FileStorage\Services\StorageManager;

use App\Modules\FileStorage\Models\File;

interface StorageInterface
{
    public function saveFile(File $file, string $uploadedFilePath): array;
    public function deleteFile(File $file, bool $backup = false): bool;
    public function fileExists(File $file): bool;
    public function isDeleted(File $file): bool;
    //public  function getDownloadUrl(File $file, bool $attachment = false, bool $private = false): string;
    public  function getDownloadPath(File $file): string;

    public function getUrl(File $file, bool $attachment = false, bool $cache = true): string;

}
