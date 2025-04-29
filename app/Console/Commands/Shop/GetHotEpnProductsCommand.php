<?php

namespace App\Console\Commands\Shop;

use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Modules\Shop\Services\EpnApiClient;
use App\Modules\Shop\Models\ShopProductParseQueue;

class GetHotEpnProductsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shop:epnHot';

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
//        $api = new EpnApiClient(
//            config('services.epn.client_id'),
//            config('services.epn.client_secret'),
//            config('services.epn.check_ip', false)
//        );
        $api = new EpnApiClient(
            'TdWjx8APQLzpa6RMoZbu1NvfhkXsK2V7', // client_id
            'XB5DUmhu70virdJ9tTW8My4SpVxAGCeR', // client_secret
            false // check_ip
        );

        $products = [];

        // Выполнение запроса
        try {
            $result = $api->get('goods/hot', [
                'filter' => '',
                'limit' => 100,
                'offset' => 0
            ]);

            $insertCount = $this->addProductsToParseQueue($result['data']);

            $output = "Income: " . count($result['data']) . "; Inserted: " . $insertCount . ";\n";
        } catch (\Exception $e) {
            $output = 'API Error: ' . $e->getMessage();
        }

        $this->info($output);
        return 0; // Возвращаем 0, если команда выполнена успешно
    }

    private function addProductsToParseQueue(array $products): int
    {
        $output = '';
        $chunkSize = 10;
        $insertedCount = 0;

        $data = array_filter($products, function($product) {
            return ($product['attributes']['offerId'] == 1 && !empty($product['attributes']['productId']));
        });


        foreach (array_chunk($data, $chunkSize) as $chunk) {
                $rows = array_map(fn($item) => [
                    'id_ae' => $item['attributes']['productId'],
                    'source' => 'epn_hot',
                    'info' => json_encode($item, JSON_UNESCAPED_UNICODE),
                    'created_at' => Carbon::now(),
                ], $chunk);

                $insertedCount += ShopProductParseQueue::insertOrIgnore($rows);
        }

        return $insertedCount;
    }
}
