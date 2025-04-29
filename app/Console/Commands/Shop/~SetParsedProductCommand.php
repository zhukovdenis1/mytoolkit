<?php

namespace App\Console\Commands\Shop;

use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Modules\Shop\Models\ShopProductParseQueue;
use App\Modules\Shop\Models\ShopProduct;
use Mockery\Exception;

class SetParsedProductCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shop:setParsedProduct
                            {id_queue : ID записи из очереди}
                            {--data= : JSON-строка с данными (обязательный)}
                            {--brcr= : JSON-строка с данными категорий (обязательный)}
                            {--error_code= : Код ошибки (необязательный)}';
//{--error_code= : Код ошибки (необязательный)}';
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

        $idQueue = $this->argument('id_queue');
        $data = $this->option('data');
        $brcr = $this->option('brcr') ?? [];

        $data = $data ? json_decode($data, true) : [];
        $brcr = $brcr ? json_decode($brcr, true) : [];

        $errorCode = $this->option('error_code') ?? 0;

        $errorCode = empty($idQueue) ? 1 : $errorCode;

        if (empty($errorCode)) {
            $queueItem = ShopProductParseQueue::findOrFail($idQueue);

            if ($queueItem['parsed_at']) {
                $this->info('Данные были распарсены ранее: ' . $queueItem['parsed_at']->format('Y-m-d H:i:s'));
                return 0;
            }

            ShopProductParseQueue::where('id', $idQueue)
                ->update([
                    'parsed_at' => Carbon::now(),
                ]);

            ShopProduct::create(
                array_merge(
                    $data,
                    [
                        'source' => $queueItem['source'] ?? null,
                        'vk_category' => $queueItem['category'] ?? null,
                        'epn_category_id' => $queueItem['info']['attributes']['goodsCategoryId'] ?? null,
                        'vk_attachment' => $queueItem['info']['vk_attachment'] ?? null,
                        'info' => $queueItem['info'] ?? null,
                    ]
                )
            );


        } else {
            ShopProductParseQueue::where('id', $idQueue)
                ->update([
                    'parsed_at' => Carbon::now(),
                    'error_code' => $errorCode
                ]);
        }


        $this->info('Полученные данные: ' . print_r($data, true));

        return 0; // Возвращаем 0, если команда выполнена успешно
    }
}
