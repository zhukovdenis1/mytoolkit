<?php

declare(strict_types=1);

namespace App\Modules\ShopArticle\Services\Shared;
use App\Helpers\EditorHelper;
use App\Modules\ShopArticle\Models\ShopArticle;
use App\Services\BaseService;
use Illuminate\Database\Eloquent\Builder;

class ShopArticleService extends BaseService
{
    public function findPaginated(array $validatedData): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $articles = ShopArticle::query()->withoutTrashed()->whereNull('code');


        $search = empty($validatedData['search']) ? '' : $validatedData['search'];
        $page = empty($validatedData['page']) ? 1 : intval($validatedData['page']);
//        $limit = empty($validatedData['_limit']) ? 10 : intval($validatedData['_limit']);
//        $sortColumn = $validatedData['_sort'] ?? 'id';
//        $order = $validatedData['_order'] ?? 'desc';
        $limit = 10;
        $sortColumn = 'id';
        $order = 'desc';

        //$articles->where('date_to', '>=', Carbon::now());


        if ($search) {
            $articles->where(function (Builder $query) use ($search) {
                $query->where('title', 'like', '%' . $search . '%');
                    //->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        $articles->orderBy($sortColumn, $order);

        //Пагинация
        $dataPaginated = $articles->paginate($limit, ['*'], 'page', $page);

        return $dataPaginated;
    }

    public function prepareForDisplay(ShopArticle $article): ShopArticle
    {
        $editorService = new EditorHelper();
        $article->text = $editorService->jsonToHtml($article->text);
        return $article;
    }
}
