<?php

declare(strict_types=1);

namespace App\Modules\FileStorage\Services\StorageManager;

use App\Modules\FileStorage\Models\Enums\StorageType;
use App\Modules\FileStorage\Models\Storage;

class StorageFactory
{
    public static function create(Storage $storage) : StorageInterface
    {
        switch ($storage->type) {
            case StorageType::HOSTING:
                return new HostingStorage();
            case StorageType::TELEGRAM:
                return new TelegramStorage($storage->data['token'], $storage->data['chat_id']);
        }
        throw new \Exception('Unknown store id');
    }
}
