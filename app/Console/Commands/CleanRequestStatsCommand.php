<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\RequestStat;
use Illuminate\Console\Command;
use Carbon\Carbon;

class CleanRequestStatsCommand extends Command
{
    protected $signature = 'stats:clean {--days=30 : Удалить записи старше X дней}';
    protected $description = 'Очистка старых записей статистики запросов';

    public function handle()
    {
        $days = (int) $this->option('days');
        $cutoffDate = Carbon::now()->subDays($days);

        $count = RequestStat::where('created_at', '<', $cutoffDate)->delete();

        $this->info("Удалено {$count} записей статистики старше {$days} дней.");

        return 0;
    }
}
