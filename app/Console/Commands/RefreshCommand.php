<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RefreshCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';


//    public function handle()
//    {
//        $name = $this->ask('Как вас зовут?');
//        $this->info("Привет, $name!");
//
//        if ($this->confirm('Хотите продолжить?')) {
//            $this->info('Продолжаем...');
//            // Дополнительная логика
//        } else {
//            $this->error('Действие отменено.');
//        }
//    }

    /**
     * Execute the console command.
     */
    public function handle()
    {

        exec("php ../artisan config:clear");
        exec("php ../artisan route:clear");
        exec("php ../artisan view:clear");

        exec("php ../artisan config:cache");
        //exec("php ../artisan route:cache");
        exec("php ../artisan view:cache");


        $this->info('ok');
        return 0; // Возвращаем 0, если команда выполнена успешно
    }
}
