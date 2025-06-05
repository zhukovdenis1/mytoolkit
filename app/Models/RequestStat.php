<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestStat extends Model
{
    protected $table = 'request_stats';

    public const UPDATED_AT = null;

    protected $fillable = [
        'total_time',
        'db_time',
        'route_name',
        'uri',
        'ip',
        'user_agent'
    ];
}
