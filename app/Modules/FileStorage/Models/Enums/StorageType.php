<?php

declare(strict_types=1);

// app/Enums/ServiceType.php
namespace App\Modules\FileStorage\Models\Enums;

enum StorageType: string
{
    case HOSTING = 'hosting';
    case TELEGRAM = 'telegram';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
