<?php

declare(strict_types=1);

namespace App\Modules\Shop\Services;
use App\Helpers\ShopArticleHelper;
use App\Modules\Shop\Models\ShopCoupon;
use App\Modules\ShopArticle\Models\ShopArticle;
use App\Services\BaseService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class ShopCouponService extends BaseService
{
    public function __construct(
        private readonly ShopArticleHelper $articleHelper
    ){}

    public function findPaginated(array $validatedData): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $articles = ShopCoupon::query();


        $search = empty($validatedData['search']) ? '' : $validatedData['search'];
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

        $articles->orderBy($sortColumn, $order);

        //Пагинация
        $dataPaginated = $articles->paginate($limit, ['*'], 'page', $page);

        return $dataPaginated;
    }

    public function getArticleData(): array
    {
        return $this->articleHelper->getDataByCode('coupons', 1);
    }
}
