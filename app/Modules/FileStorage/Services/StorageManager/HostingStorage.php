<?php

declare(strict_types=1);

namespace App\Modules\FileStorage\Services\StorageManager;

use App\Modules\FileStorage\Models\File;

class HostingStorage extends BaseStorage implements StorageInterface
{
    public function __construct()
    {
        parent::__construct(false);
    }
    public function saveFile(File $file, string $uploadedFilePath): array
    {
        $fullPath = $this->getFullPath($file);
        $success = true;

        if (file_exists($fullPath)) {
            $success =  false;
        } else {
            if (!is_dir(dirname($fullPath))) {
                mkdir(dirname($fullPath), 0644, true);
            }

            copy($uploadedFilePath, $fullPath);

            if (!file_exists($fullPath)) {
                $success =  false;
            }
        }

        return ['success' => $success, 'data' => []];
    }

    public function deleteFile(File $file, bool $backup = false): bool
    {
        $fullPath = $this->getFullPath($file);

        if (!is_file($fullPath)) {
            $success = true;
        } else {
            $success = unlink($fullPath);
        }

        return $success;
    }

    public function fileExists(File $file): bool
    {
        $fullPath = $this->getFullPath($file);

        return is_file($fullPath);
    }

    public function isDeleted(File $file): bool
    {
        return !$this->fileExists($file);
    }

    public function getUrl(File $file, bool $attachment = false, bool $cache = true): string
    {
        if ($attachment) {
            return '/' . $this->getUri($file).'?attachement=1';
        } else {
            return '/' . $this->getUri($file);
        }
    }

    public function getDownloadPath(File $file): string
    {
        return $this->getFullPath($file);
    }

    private function getUri(File $file): string
    {
        return 'uploads/storage/' . $file->user_id . '/' . $file->module_name . '/'
            . $file->module_id . '/'. $file->id . '_' . $file->name . '.' . $file->ext;
    }

    private function getPath(File $file): string
    {
        if ($file->private_hash) {
            return $this->getUri($file) . '_' . $file->private_hash;
        } else {
            return $this->getUri($file);
        }
    }

    private function getFullPath(File $file): string
    {
        return getcwd() . '/' . $this->getPath($file);
    }


}
