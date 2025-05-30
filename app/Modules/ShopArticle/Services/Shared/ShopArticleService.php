<?php

declare(strict_types=1);

namespace App\Modules\ShopArticle\Services\Shared;
use App\Helpers\EditorHelper;
use App\Helpers\ShopArticleHelper;
use App\Modules\ShopArticle\Models\ShopArticle;
use App\Services\BaseService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class ShopArticleService extends BaseService
{
    public function __construct(
        private readonly ShopArticleHelper $articleHelper,
        private readonly EditorHelper $editorHelper
    ){}
    public function findPaginated(array $validatedData): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $articles = ShopArticle::query()->withoutTrashed()->whereNull('code');


        $search = empty($validatedData['search']) ? '' : $validatedData['search'];
        $page = empty($validatedData['page']) ? 1 : intval($validatedData['page']);
//        $limit = empty($validatedData['_limit']) ? 10 : intval($validatedData['_limit']);
//        $sortColumn = $validatedData['_sort'] ?? 'id';
//        $order = $validatedData['_order'] ?? 'desc';
        $limit = 50;
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

        foreach ($dataPaginated as $article) {
            $article->h1 = $this->articleHelper->replace($article->h1);
        }

        return $dataPaginated;
    }

    public function prepareForDisplay(ShopArticle $article): ShopArticle
    {
        $article->title = $this->articleHelper->replace($article->title);
        $article->text = $this->editorHelper->jsonToHtml($article->text, $article->title);
        $article->text = $this->articleHelper->replace($article->text);
        $article->h1 = $this->articleHelper->replace($article->h1);
        $article->keywords = $this->articleHelper->replace($article->keywords);
        $article->description = $this->articleHelper->replace($article->description);
        return $article;
    }

    public function getMainPageArticles(): Collection
    {
        $articles = ShopArticle::select('id', 'h1', 'uri')
            ->whereNull('code')
            ->orderBy('id', 'desc')
            ->limit(20)
            ->get();

        $articles->each(function ($article) {
            $article->h1 = $this->articleHelper->replace($article->h1);
        });

        return $articles;
    }
}
