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
        $schedule->exec('php parser/parser_reviews.php')->everyTwoMinutes();
        $schedule->command('shop:refreshImportantProducts')->hourly();
        $schedule->command('shop:parseVkGroups')->everyTwoHours();
        $schedule->command('shop:post')->dailyAt('04:00');
        $schedule->command('shop:post')->dailyAt('09:00');
        $schedule->command('shop:post')->dailyAt('14:00');
        $schedule->command('shop:genArticles')->dailyAt('17:00');
        $schedule->command('shop:genArticles')->dailyAt('17:30');
        $schedule->command('shop:genArticles')->dailyAt('18:00');
        $schedule->command('shop:genArticles')->dailyAt('18:30');
        $schedule->command('shop:genArticles')->dailyAt('19:00');
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
