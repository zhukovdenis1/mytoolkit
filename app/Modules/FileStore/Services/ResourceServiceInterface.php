<?php

declare(strict_types=1);

namespace App\Modules\FileStore\Services;

interface ResourceServiceInterface
{
    public function save(string $name);
}
