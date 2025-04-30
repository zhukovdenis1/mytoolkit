<?php

declare(strict_types=1);

namespace App\Modules\FileStorage\Services\StorageManager;

use App\Modules\FileStorage\Models\File;


class StorageCache
{
    private const MAX_CACHE_FILE_SIZE = 5000000;//5Mb

    public function allowed(FIle $file): bool
    {
        return $file->size <= self::MAX_CACHE_FILE_SIZE;
    }
    public function save(File $file, string $uploadedFilePath): bool
    {
        if (!$this->allowed($file)) {
            return false;
        }

        $fullPath = $this->getFullPath($file);

        if (file_exists($fullPath)) {
            return false;
        } else {
            if (!is_dir(dirname($fullPath))) {
                mkdir(dirname($fullPath), 0644, true);
            }

            copy($uploadedFilePath, $fullPath);

            if (!file_exists($fullPath)) {
                return false;
            }
        }
        return true;
    }

    public function refresh(File $file, string $path): bool
    {
        if (!$this->allowed($file)) {
            return false;
        }
        return (bool)file_put_contents($this->getFullPath($file), file_get_contents($path));
    }

    public function delete(File $file): bool
    {
        $fullPath = $this->getFullPath($file);

        if (!is_file($fullPath)) {
            $success = true;
        } else {
            $success = unlink($fullPath);
        }

        return $success;
    }

    public function exists(File $file): bool
    {
        return is_file($this->getFullPath($file));
    }

    public function getUri(File $file): string
    {
        return 'uploads/storage/' . $file->user_id . '/' . $file->module_name . '/'
            . $file->module_id . '/'. $file->id . '_' . $file->name . '.' . $file->ext;
    }

    public function getPath(File $file): string
    {
        if ($file->private_hash) {
            return $this->getUri($file) . '_' . $file->private_hash;
        } else {
            return $this->getUri($file);
        }
    }

    public function getFullPath(File $file): string
    {
        return getcwd() . '/' . $this->getPath($file);
    }
}
