<?php

declare(strict_types=1);

namespace App\Modules\Shop\Services;
use App\Helpers\ShopArticleHelper;
use App\Modules\Shop\Models\ShopCoupon;
use App\Modules\ShopArticle\Models\ShopArticle;
use App\Services\BaseService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ShopCouponService extends BaseService
{
    public function __construct(
        private readonly ShopArticleHelper $articleHelper
    ){}

    public function findPaginated(array $validatedData): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $coupons = ShopCoupon::query();


        $search = $validatedData['search'] ?? '';
        $type = $validatedData['type'] ?? null;
        $page = empty($validatedData['page']) ? 1 : intval($validatedData['page']);
        $limit = empty($validatedData['_limit']) ? 30 : intval($validatedData['_limit']);
        $sortColumn = $validatedData['_sort'] ?? 'id';
        $order = $validatedData['_order'] ?? 'desc';

        $coupons->where('date_to', '>=', Carbon::now());


        if ($search) {
            $coupons->where(function (Builder $query) use ($search) {
                $query->where('title', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        if ($type == 'code') {
            $coupons->whereNotNull('code');
        } elseif ($type == 'discount_amount') {
            $coupons->where('discount_amount', '>', 0);
        } elseif ($type == 'discount_percent') {
            $coupons->where('discount_percent', '>', 0);
        }

        $coupons->orderBy($sortColumn, $order);
//var_dump($coupons->toSql());die;
        //Пагинация
        $dataPaginated = $coupons->paginate($limit, ['*'], 'page', $page);

        return $dataPaginated;
    }

    public function getArticleData(): array
    {
        return $this->articleHelper->getDataByCode('coupons');
    }

    public function getCounts(): array
    {
        $now = Carbon::now()->toDateTimeString();
        $counts = DB::table('shop_coupons')
            ->selectRaw('COUNT(CASE WHEN code IS NOT NULL AND date_to >= "' . $now . '" THEN 1 END) as code_not_null_count')
            ->selectRaw('COUNT(CASE WHEN discount_amount > 0  AND date_to >= "' . $now . '" THEN 1 END) as discount_amount_positive_count')
            ->selectRaw('COUNT(CASE WHEN discount_percent > 0  AND date_to >= "' . $now . '" THEN 1 END) as discount_percent_positive_count')
            ->selectRaw('COUNT(CASE WHEN date_to >= "' . $now . '" THEN 1 END) as total')
            ->first();

        $result = [];
        $result['code'] = $counts->code_not_null_count;
        $result['discount_amount'] = $counts->discount_amount_positive_count;
        $result['discount_percent'] = $counts->discount_percent_positive_count;
        $result['total'] = $counts->total;

        return $result;
    }

    public function getMainPageCoupons():  Collection
    {
        $coupons = ShopCoupon::query()
            ->where('date_to', '>=', Carbon::now())
            ->where(function ($query) {
                $query->where('discount_amount', '>', 0)
                    ->orWhere('discount_percent', '>', 0);
            })
            ->orderByRaw('CASE WHEN discount_amount > 0 THEN discount_amount ELSE 0 END DESC')
            ->orderByRaw('CASE WHEN discount_percent > 0 THEN discount_percent ELSE 0 END DESC')
            ->limit(10)
            ->get();

        return $coupons;
    }
}
