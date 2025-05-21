<?php

declare(strict_types=1);

namespace App\Modules\Shop\Http\Controllers\Admin;


use App\Http\Controllers\BaseController;
use App\Http\Resources\AnonymousResource;
use App\Modules\Shop\Http\Requests\Admin\StoreShopParsingRequest;
use App\Modules\Shop\Models\ShopProductParseQueue;
use Carbon\Carbon;
use DOMDocument;
use DOMXPath;
use Illuminate\Support\Facades\DB;


class ShopParsingController extends BaseController
{
    public function __construct()
    {
    }

    public function getEpnCategories():AnonymousResource
    {
        return new AnonymousResource(config('epn.categories'));
    }


    public function store(StoreShopParsingRequest $request): AnonymousResource
    {
        $validated = $request->validated();
        $important = $validated['important'] ? 1 : 0;
        $categoryId = $validated['category_id'] ?? 0;

        $queue = [];
        try {
            $xml = simplexml_load_string(trim($validated['data']));
            // Создаем массив для результатов
            $products = [];

            foreach ($xml->shop->offers->offer as $offer) {
                if ($offer['available']) {
                    $queue[] = [
                        'source' => 'epn_top',
                        'important' => $important ? 1 : 0,
                        'id_ae' => (string)$offer['id'],
                        'created_at' => Carbon::now(),
                        'info' => json_encode([
                            'epnCategoryId' => (string)$offer->categoryId,
                            'name' => (string)$offer->name
                        ], JSON_UNESCAPED_UNICODE)
                    ];
                }
            }

        } catch (\Exception $e) {
            // Получаем очищенные ID
            $strings = array_filter(
                array_map('trim', explode("\n", $validated['data'])),
                fn($id) => !empty($id)
            );

            if (is_numeric($strings[0])) {//просто id товаров
                foreach ($strings as $id) {
                    $queue[] = [
                        'important' => $important ? 1 : 0,
                        'id_ae' => (string)$id,
                        'info' => json_encode([
                            'epnCategoryId' => $categoryId,
                        ], JSON_UNESCAPED_UNICODE),
                        'created_at' => Carbon::now(),
                    ];
                }
            } else {
                $data = $this->parseHtmlTable($validated['data']);
                foreach ($data as $row) {
                    $queue[] = [
                        'source' => 'epn_top',
                        'important' => $important ? 1 : 0,
                        'id_ae' => $row['id_ae'],
                        'info' => json_encode(array_merge(
                            [
                                'date' => date('Y-m', strtotime('last month')),
                                'epnCategoryId' => $categoryId,
                            ], $row), JSON_UNESCAPED_UNICODE),
                        'created_at' => Carbon::now(),
                    ];
                }
            }
        }


        $countIncome = count($queue);

        // Получаем общее количество записей ДО вставки
        $countBefore = ShopProductParseQueue::count();


        // Вставка чанками через INSERT IGNORE
        foreach (array_chunk($queue, 100) as $chunk) {
            DB::table('shop_products_parse_queue')->insertOrIgnore($chunk);
        }

        // Получаем общее количество ПОСЛЕ вставки
        $countAfter = ShopProductParseQueue::count();

        // Вычисляем количество добавленных записей
        $insertedCount = $countAfter - $countBefore;

        return new AnonymousResource([
            'message' => "Передано: {$countIncome}. Добавлено новых записей: {$insertedCount}"
        ]);
    }

    private function parseHtmlTable($html) {
        // Указываем кодировку для DOMDocument
        $dom = new DOMDocument('1.0', 'UTF-8');
        // Подавляем ошибки парсинга
        @$dom->loadHTML('<?xml encoding="UTF-8">' . $html);

        // Убедимся, что все внутренние обработки в UTF-8
        $dom->encoding = 'UTF-8';

        $xpath = new DOMXPath($dom);
        $result = [];

        $rows = $xpath->query('//table/tbody/tr');

        foreach ($rows as $row) {
            $rowData = [];

            // Позиция (первая колонка)
            $position = $xpath->query('.//td[1]', $row)->item(0)->nodeValue;
            $rowData['position'] = trim($position);

            $order = count($rows) + 1 - intval($position);
            //$order = str_pad(strval($order), 3, '0', STR_PAD_LEFT);
            $rowData['order'] = $order;

            // Ссылка на товар
            $linkNode = $xpath->query('.//td[2]/a', $row)->item(0);
            $link = $linkNode ? $linkNode->getAttribute('href') : '';
            $link = trim($link);
            $idAE = '';
            if (preg_match('/\/item\/-\/(\d+)\.html/', $link, $matches)) {
                $idAe = $matches[1];
            }
            $rowData['id_ae'] = $idAe;



            // Название товара
            $title = $linkNode ? $linkNode->nodeValue : '';
            $rowData['title'] = trim($title);

            // Доход (третья колонка)
            $income = $xpath->query('.//td[3]', $row)->item(0)->nodeValue;
            $rowData['income'] = trim($income);

            // Количество заказов (четвертая колонка)
            $orders = $xpath->query('.//td[4]', $row)->item(0)->nodeValue;
            $rowData['orders_amount'] = trim($orders);

            // Цена (пятая колонка)
            $price = $xpath->query('.//td[5]', $row)->item(0)->nodeValue;
            $rowData['price'] = trim($price);

            $rowData['cashback'] = ceil(intval($rowData['income']) / intval($rowData['orders_amount']));

            $result[] = $rowData;
        }

        return $result;
    }
}
