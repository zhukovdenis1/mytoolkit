<?php

declare(strict_types=1);

namespace App\Modules\Shop\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Http\Resources\AnonymousResource;
use App\Modules\Shop\Http\Requests\Shared\SetParsedProductRequest;
use App\Modules\Shop\Models\ShopCategory;
use App\Modules\Shop\Models\ShopProduct;
use App\Modules\Shop\Models\ShopProductParseQueue;
use Carbon\Carbon;
use Illuminate\Http\Request;


class ShopParseController extends Controller
{

    public function getProductForParse(Request $request): AnonymousResource
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

        return new AnonymousResource($data);
    }

    public function setParsedProduct(SetParsedProductRequest $request)
    {
        $newProduct = null;

        $idQueue = $request->input('id_queue');
        $data = $request->input('data') ?? [];
        $brcr = $request->input('brcr') ?? [];
        $errorCode = $request->input('error_code') ?? 0;

        $queueItem = ShopProductParseQueue::findOrFail($idQueue);

        if ($queueItem['parsed_at']) {
            //throw new \Exception('Данные были распарсены ранее: ' . $queueItem['parsed_at']->format('Y-m-d H:i:s'));
            return new AnonymousResource(['error' => 'Данные были распарсены ранее: ' . $queueItem['parsed_at']->format('Y-m-d H:i:s')]);
        }

        $categroryParents = [];
        for ($i =0; $i < count($brcr); $i++) {
            $b = $brcr[$i];
            $b['level'] = $i;
            if ($i) {
                $b['parent_id'] = $brcr[$i-1]['id_ae'];
                $categroryParents[] = $b['parent_id'];
                $b['parents'] = implode(',', $categroryParents);
            } else {
                $b['parent_id'] = null;
                $b['parents'] = null;
            }
            try {
                ShopCategory::create($b);
            } catch (\Exception $e) {

            }
        }

        try {
            if (empty($idQueue)) {
                throw new \Exception('1');
            }

            $newProduct = ShopProduct::create(
                array_merge(
                    $data,
                    [
                        'source' => $queueItem['source'] ?? null,
                        'title_source' => $queueItem['info']['attributes']['titleRu'] ?? null,
                        'vk_category' => $queueItem['info']['vk_category'] ?? null,
                        'epn_category_id' => $queueItem['info']['attributes']['goodsCategoryId'] ?? null,
                        'vk_attachment' => $queueItem['info']['vk_attachment'] ?? null,
                        'info' => $queueItem['info'] ?? null,
                    ]
                )
            );

            ShopProductParseQueue::where('id', $idQueue)
                ->update([
                    'parsed_at' => Carbon::now(),
            ]);
        } catch (\Exception $e) {
            $errorCode = intval($e->getMessage());
            $errorCode = $errorCode ?: 6;
        }

        ShopProductParseQueue::where('id', $idQueue)
            ->update([
                'parsed_at' => Carbon::now(),
                'error_code' => $errorCode
            ]);

        return new AnonymousResource($newProduct);
    }
}
