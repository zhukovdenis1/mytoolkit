<?php

namespace App\Console\Commands\Shop;

use App\Modules\Shop\Models\ShopCoupon;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Modules\Shop\Services\EpnApiClient;

class GetEpnCouponsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shop:epnCoupons';

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
        $api = new EpnApiClient(
            config('epn.id'), // client_id
            config('epn.secret'), // client_secret
            config('epn.check_ip') // check_ip
        );


        // Выполнение запроса
        try {
            $result = $api->get('coupons', [
                'offerId' => 1,
                'limit' => 100,
                'offset' => 0
            ]);

            $insertCount = $this->addCoupons($result['data']);

            $output = "Income: " . count($result['data']) . "; Inserted: " . $insertCount . ";\n";
        } catch (\Exception $e) {
            $output = 'API Error: ' . $e->getMessage();
        }

        $this->info($output);
        return 0; // Возвращаем 0, если команда выполнена успешно
    }

    private function addCoupons(array $data): int
    {
        $output = '';
        $chunkSize = 100;
        $insertedCount = 0;


        foreach (array_chunk($data, $chunkSize) as $chunk) {
                $rows = array_map(fn($item) => [
                    'epn_id' => $item['id'],
                    'code' => $item['attributes']['code'] ?? null,
                    'url' => $item['attributes']['url'] ?? null,
                    'uri' => $item['attributes']['slug'] ?? null,
                    'date_from' => $item['attributes']['dateFrom'] ?? null,
                    'date_to' => $item['attributes']['dateTo'] ?? null,
                    'title' => $item['attributes']['name'] ?? null,
                    'description' => $item['attributes']['description'] ?? null,
                    'info' => json_encode($item, JSON_UNESCAPED_UNICODE),
                    'created_at' => Carbon::now(),
                ], $chunk);

                $insertedCount += ShopCoupon::insertOrIgnore($rows);
        }

        return $insertedCount;
    }
}
