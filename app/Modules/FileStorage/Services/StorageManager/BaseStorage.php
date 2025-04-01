<?php

declare(strict_types=1);

namespace App\Modules\FileStorage\Services\StorageManager;

abstract class BaseStorage implements StorageInterface
{
    public readonly ?StorageCache $cache;

    public function __construct(bool $useCache)
    {
        if ($useCache) {
            $this->cache = new StorageCache();
        } else {
            $this->cache = null;
        }

    }
}
