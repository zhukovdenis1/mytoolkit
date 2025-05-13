<?php

namespace App\Console\Commands\Shop;

use App\Helpers\StringHelper;
use App\Modules\Shop\Models\ShopCoupon;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Modules\Shop\Services\EpnApiClient;

class GetCouponsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shop:coupons';

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

    public function __construct(private readonly StringHelper $stringHelper) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $output = '';

        try {
            $output = $this->getEpnCoupons();
            $output .= $this->getPikabuCoupons();
        } catch (\Exception $e) {
            $output .= $e->getMessage();
        }

        $this->info($output);
        return 0; // Возвращаем 0, если команда выполнена успешно
    }

    private function getEpnCoupons(): string
    {
        $api = new EpnApiClient(
            config('epn.id'), // client_id
            config('epn.secret'), // client_secret
            config('epn.check_ip') // check_ip
        );

        try {
            $result = $api->get('coupons', [
                'offerId' => 1,
                'limit' => 100,
                'offset' => 0
            ]);

            $data = [];

            foreach ($result['data'] as $item) {
                $data[] = [
                    'epn_id' => $item['id'],
                    'code' => empty($item['attributes']['code']) ? null : $item['attributes']['code'],
                    'url' => $item['attributes']['url'] ?? null,
                    'uri' => $item['attributes']['slug'] ?? null,
                    'date_from' => $item['attributes']['dateFrom'] ?? null,
                    'date_to' => $item['attributes']['dateTo'] ?? null,
                    'title' => $item['attributes']['name'] ?? null,
                    'description' => $item['attributes']['description'] ?? null,
                    'info' => json_encode($item, JSON_UNESCAPED_UNICODE),
                ];
            }

            $insertCount = $this->addCoupons($data, 'epn_id');

            $output = "Epn income: " . count($result['data']) . "; Inserted: " . $insertCount . ";\n";
        } catch (\Exception $e) {
            $output = 'Epn API Error: ' . $e->getMessage();
        }

        return $output;
    }

    private function getPikabuCoupons(): string
    {
        $content = file_get_contents('https://promokod.pikabu.ru/shops/aliexpress');
        $json = '';
        if (preg_match('/<script id="vike_pageContext" type="application\/json">(.*?)<\/script>/s', $content, $matches)) {
            $json = $matches[1];
        } else {
            throw new \Exception('Unable to get json from pikabu content');
        }

        $data = json_decode($json, true);

        if (!$data) {
            throw new \Exception('Unable to parse json string in pikabu content');
        }

        $coupons = [];

        foreach ($data["data"]["coupons"]["active"] as $c) {
            $coupons[] = $c;
        }

        $data = [];

        foreach ($coupons as $item) {
            $now = Carbon::now();
            if ($item['shop']['id'] == 9) {
                $data[] = [
                    'pikabu_id' => $item['couponId'],
                    'code' => empty($item['promoCode']) ? null : $item['promoCode'],
                    'url' => null,
                    'uri' => $this->stringHelper->buildUri($item['title']),
                    'date_from' => new Carbon($item['startDate']),
                    'date_to' => new Carbon($item['endDate']),
                    'title' => $item['title'] ?? null,
                    'description' => $item['description'] ?? null,
                    'discount_amount' => (int)$item['discountAmount'] ?? 0,
                    'discount_percent' => (int)$item['discountPercent'] ?? 0,
                    'info' => json_encode($item, JSON_UNESCAPED_UNICODE),
                ];
            }
        }

        $insertCount = $this->addCoupons($data,'pikabu_id');

        $output = "Pikabu income: " . count($data) . "(" . count($coupons) . "); Inserted: " . $insertCount . ";\n";

        return $output;
    }

    private function addCoupons(array $data, string $primaryKeyName): int
    {
        $chunkSize = 1;
        $insertedCount = 0;


        /*foreach (array_chunk($data, $chunkSize) as $chunk) {
                $rows = array_map(fn($item) => $item, $chunk);

                $insertedCount += ShopCoupon::insertOrIgnore($rows);
        }*/

        $countBefore = ShopCoupon::whereNotNull($primaryKeyName)->count();

        foreach (array_chunk($data, $chunkSize) as $chunk) {
            $insertedCount += ShopCoupon::upsert(
                $chunk,
                [$primaryKeyName], // Укажите здесь имя первичного ключа или уникального индекса
                array_keys($chunk[0] ?? []) // Все поля для обновления при дубликате
            );
        }

        $countAfter = ShopCoupon::whereNotNull($primaryKeyName)->count();

        //return $insertedCount;
        return $countAfter - $countBefore;
    }
}
