<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DeployCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deploy {--branch=main}';

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
        $output = "GIT PULL FROM MAIN BRANCH" . PHP_EOL;

//        $this->info($output);
//        return 0; // Возвращаем 0, если команда выполнена успешно

        // Checks start
        if(function_exists('exec')) {
            $output .= "exec is enabled";
        }

        $output .= PHP_EOL;
        $output .= exec('whoami');
        $output .= PHP_EOL;
        //$output .= exec('which git');
        //$output .= PHP_EOL;
        // Checks end

        $output = getcwd();

        $repositoryPath = getcwd();


        // Set the branch you want to pull from

        $branch = $this->option('branch') ?? 'main';

        $result = exec("cd {$repositoryPath} &&  git fetch origin && git reset --hard origin/main && git pull origin {$branch} 2>&1", $r2);
        $result = exec("git pull 2>&1", $r2);

        foreach ($r2 as $line) {
            $output .= $line . "\n";
        }

        unset($r2);

        $output .= "\n\n";
        $output .= "------------------------------------------------------";
        $output .= "\ngit status\n";
        $output .= "------------------------------------------------------";
        $output .= "\n\n";

        $result = exec("git status 2>&1", $r2);

        foreach ($r2 as $line) {
            $output .= $line . "\n";
        }
        //$result = exec("php -r 'echo getcwd();'", $r2);

        $result = exec("php ./artisan migrate 2>&1", $r2);

        foreach ($r2 as $line) {
            $output .= $line . "\n";
        }

        $this->info($output);
        return 0; // Возвращаем 0, если команда выполнена успешно
    }
}
