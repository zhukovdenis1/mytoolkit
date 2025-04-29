<?php

namespace App\Console\Commands\Shop;

use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Modules\Shop\Models\ShopProductParseQueue;

class GetProductForParseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shop:getProductForParse';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $data = ShopProductParseQueue::query()
            ->whereNull('parsed_at')
            ->where(function($query) {
                $query->whereNull('blocked_until')
                    ->orWhere('blocked_until', '<', Carbon::now());
            })
            ->orderByDesc('important')
            ->orderBy('created_at')
            ->first();

        if ($data->exists) {
            ShopProductParseQueue::where('id', $data->id)
                ->update([
                    'blocked_until' => Carbon::now()->addHour()
                ]);
        }

        $this->info($data->toJson());



        return 0; // Возвращаем 0, если команда выполнена успешно
    }
}
