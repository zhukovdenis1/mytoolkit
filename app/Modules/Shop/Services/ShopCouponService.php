<?php

declare(strict_types=1);

namespace App\Modules\Shop\Services;
use App\Helpers\ShopArticleHelper;
use App\Modules\Shop\Models\ShopCoupon;
use App\Modules\ShopArticle\Models\ShopArticle;
use App\Services\BaseService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ShopCouponService extends BaseService
{
    public function __construct(
        private readonly ShopArticleHelper $articleHelper
    ){}

    public function findPaginated(array $validatedData): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $articles = ShopCoupon::query();


        $search = $validatedData['search'] ?? '';
        $type = $validatedData['type'] ?? null;
        $page = empty($validatedData['page']) ? 1 : intval($validatedData['page']);
        $limit = empty($validatedData['_limit']) ? 30 : intval($validatedData['_limit']);
        $sortColumn = $validatedData['_sort'] ?? 'id';
        $order = $validatedData['_order'] ?? 'desc';

        $articles->where('date_to', '>=', Carbon::now());


        if ($search) {
            $articles->where(function (Builder $query) use ($search) {
                $query->where('title', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        if ($type == 'code') {
            $articles->whereNotNull('code');
        } elseif ($type == 'discount_amount') {
            $articles->where('discount_amount', '>', 0);
        } elseif ($type == 'discount_percent') {
            $articles->where('discount_percent', '>', 0);
        }

        $articles->orderBy($sortColumn, $order);
//var_dump($articles->toSql());die;
        //Пагинация
        $dataPaginated = $articles->paginate($limit, ['*'], 'page', $page);

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
}
