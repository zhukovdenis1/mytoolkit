<?php
/**
 * php artisan schedule:list
 * php artisan schedule:run
 */
namespace App\Scheduling;

use Illuminate\Console\Scheduling\Schedule;

class AppSchedule
{
    public static function handle(Schedule $schedule): void
    {
        //$schedule->command('shop:coupons')->dailyAt('01:00');
        //$schedule->exec('php parser/parser.php')->everyMinute();
        $schedule->exec('php parser/parser.php')->everyTenMinutes();
        $schedule->command('shop:refreshImportantProducts')->hourly();
        $schedule->command('shop:parseVkGroups')->everyTwoHours();
        $schedule->command('shop:post')->dailyAt('07:00');
        $schedule->command('shop:post')->dailyAt('13:00');
        $schedule->command('shop:post')->dailyAt('17:00');
        //$schedule->command('stats:clean --days=30')->daily();
    }
}
/* с логированием
$schedule->exec('php ' . base_path('parser/parser.php'))
    ->everyMinute()
    ->appendOutputTo(storage_path('logs/parser.log'));
*/
/*
$schedule->call(function () {
    Log::channel('daily')->info('Запуск parser.php через планировщик');

    // или любой другой код
})->everyMinute();
*/
