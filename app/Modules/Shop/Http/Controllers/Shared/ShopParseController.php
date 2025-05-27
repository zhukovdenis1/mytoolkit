<?php

declare(strict_types=1);

namespace App\Modules\Shop\Http\Controllers\Shared;

use App\Helpers\StringHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\AnonymousResource;
use App\Modules\Shop\Http\Requests\Shared\SetParsedProductRequest;
use App\Modules\Shop\Models\ShopCategory;
use App\Modules\Shop\Models\ShopCoupon;
use App\Modules\Shop\Models\ShopProduct;
use App\Modules\Shop\Models\ShopProductParseQueue;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class ShopParseController extends Controller
{
    public function __construct(private readonly StringHelper $stringHelper) {}

    public function getProductForParse(Request $request): AnonymousResource
    {
        $data = ShopProductParseQueue::query()
            ->whereNull('parsed_at')
            //->where('source', '=','epn_top')
            ->where(function($query) {
                $query->whereNull('blocked_until')
                    ->orWhere('blocked_until', '<', Carbon::now());
            })
            ->orderByDesc('important')
            ->orderBy('created_at')
            ->first();

        if ($data->exists) {
            if ($data->important) {
                $blockedUntil = Carbon::now()->addMinutes(3);
            } else {
                $blockedUntil = Carbon::now()->addHour();
            }
            ShopProductParseQueue::where('id', $data->id)
                ->update([
                    'blocked_until' => $blockedUntil
                ]);
        }

        return new AnonymousResource($data);
    }

    public function setParsedProduct(SetParsedProductRequest $request)
    {
        $newProduct = null;
        $errorMessage = '';
        $version = 0;

        try {
            $idQueue = $request->input('id_queue');
            $data = $request->input('data') ?? [];
            $brcr = $request->input('brcr') ?? [];
            $errorCode = $request->input('error_code') ?? 0;
            $version = $request->input('version') ?? 0;

            $queueItem = ShopProductParseQueue::findOrFail($idQueue);

            if ($queueItem['parsed_at'] && $errorCode) {
                //throw new \Exception('Данные были распарсены ранее: ' . $queueItem['parsed_at']->format('Y-m-d H:i:s'));
                return new AnonymousResource(['error' => 'Данные были распарсены ранее: ' . $queueItem['parsed_at']->format('Y-m-d H:i:s')]);
            }

            if ($errorCode) {
                throw new \Exception($errorCode);
            }

            $categoryParents = [];
            for ($i =0; $i < count($brcr); $i++) {
                $b = $brcr[$i];
                $b['level'] = $i;
                if ($i) {
                    $b['parent_id'] = $brcr[$i-1]['id_ae'];
                    $categoryParents[] = $b['parent_id'];
                    $b['parents'] = implode(',', $categoryParents);
                } else {
                    $b['parent_id'] = null;
                    $b['parents'] = null;
                }
                try {
                    ShopCategory::create($b);
                } catch (\Exception $e) {

                }
            }

            if (empty($idQueue)) {
                throw new \Exception('1');
            }

            $titleSource = null;
            $titleSource = $queueItem['info']['attributes']['titleRu'] ?? $titleSource;
            $titleSource = $queueItem['info']['title'] ?? $titleSource;

            $epnCategoryId = null;
            $epnCategoryId = $queueItem['info']['attributes']['goodsCategoryId'] ?? $epnCategoryId;
            $epnCategoryId = $queueItem['info']['epnCategoryId'] ?? $epnCategoryId;

            $epnCashBack = 0;
            $epnCashBack = $queueItem['info']['cashback'] ?? $epnCashBack;
            if (isset($queueItem['info']['attributes']['cashbackPercent'])) {
                $epnCashBack = floatval($queueItem['info']['attributes']['cashbackPercent'])* intval($data['price'])/100;
            }
            $epnCashBack = intval($epnCashBack);

            $epnIncome = $queueItem['info']['income'] ?? 0;
            $epnIncome = intval($epnIncome);

            /*$newProduct = ShopProduct::create(
                array_merge(
                    $data,
                    [
                        'source' => $queueItem['source'] ?? null,
                        'title_source' => $titleSource,
                        'vk_category' => $queueItem['info']['vk_category'] ?? null,
                        'epn_category_id' => $epnCategoryId,
                        'vk_attachment' => $queueItem['info']['vk_attachment'] ?? null,
                        'info' => $queueItem['info'] ?? null,
                    ]
                )
            );*/

            $hruTitle = $data['title_ae']
                ? mb_substr($this->stringHelper->buildUri($data['title_ae']), 0, 100)
                : null;

            $newProduct = ShopProduct::updateOrCreate(
                [
                    'id_ae' => $data['id_ae']
                ],
                array_merge(
                    $data,
                    [
                        'hru' => $hruTitle,
                        'source' => $queueItem['source'] ?? null,
                        'title_source' => $titleSource,
                        'vk_category' => $queueItem['info']['vk_category'] ?? null,
                        'epn_category_id' => $epnCategoryId,
                        'vk_attachment' => $queueItem['info']['vk_attachment'] ?? null,
                        'epn_month_income' => $epnIncome,
                        'epn_cashback' => $epnCashBack,
                        'info' => $queueItem['info'] ?? null,
                    ]
                )
            );

            if ($epnCategoryId) {
                ShopCategory::where('id_ae', $data['category_id'])->update([
                    'id_epn' => $epnCategoryId,
                ]);
            }

            ShopProductParseQueue::where('id', $idQueue)
                ->update([
                    'parsed_at' => Carbon::now(),
                    'version' => $version,
                ]);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            $errorCode = intval($e->getMessage());
            $errorCode = $errorCode ?: -1;

            if ($e instanceof QueryException) {
                $errorCode = 6;
                Log::channel('sql_error')->error('Database Query Error', [
                    'message' => $e->getMessage(),
                    'sql' => $e->getSql(),
                    'bindings' => $e->getBindings(),
                    //'trace' => $e->getTraceAsString(),
                    'url' => $request->fullUrl(),
                    'ip' => $request->ip(),
                ]);
            }
        }

        ShopProductParseQueue::where('id', $idQueue)
            ->update([
                'parsed_at' => Carbon::now(),
                'error_code' => $errorCode,
                'version' => $version
            ]);

        return new AnonymousResource(['product' => $newProduct, 'message' => $errorMessage]);
    }

    public function getCouponForParse(Request $request): AnonymousResource
    {
        $data = ShopCoupon::query()
            ->whereNotNull('pikabu_id')
            ->whereNull('url')
            ->orderBy('created_at')
            ->first();

        return new AnonymousResource($data);
    }

    public function setParsedCoupon(Request $request)
    {
        $validated = $request->validate([
            'coupon_id' => ['required', 'integer'],
            'url' => ['nullable', 'string', 'min:0', 'max:1000'],
        ]);
        $coupon = ShopCoupon::query()->findOrFail($validated['coupon_id']);
        $coupon->url = $validated['url'];
        $coupon->save();

        return new AnonymousResource($coupon);
    }
}
