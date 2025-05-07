<?php

declare(strict_types=1);

namespace App\Modules\ShopArticle\Services\Admin;

use App\Exceptions\ErrorException;
use App\Helpers\Helper;
use App\Modules\ShopArticle\Models\ShopArticle;
use App\Services\BaseService;
use Illuminate\Database\Eloquent\Builder;

class ShopArticleService extends BaseService
{
    public function create(array $validatedData): ShopArticle
    {
        $validatedData['uri'] = $this->generateUri($validatedData);
        $article =  ShopArticle::create($validatedData);
        return $article;
    }


    public function updateContent(array $validatedData, ShopArticle $article): ShopArticle
    {
        $article->update($validatedData);

        if (!$article->wasChanged()) {
            throw new ErrorException('Data was not changed');
        }

        return $article;
    }

    public function findPaginated(array $validatedData): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $articles = ShopArticle::query();

        //$notes = Note::where('user_id', $validatedData['user_id']);

        $search = $validatedData['search'] ?? null;
        $page = empty($validatedData['_page']) ? 1 : intval($validatedData['_page']);
        $limit = empty($validatedData['_limit']) ? 10 : intval($validatedData['_limit']);
        $sortColumn = $validatedData['_sort'] ?? 'id';
        $order = $validatedData['_order'] ?? 'desc';


        if ($search) {
            $articles->where(function (Builder $query) use ($search) {
                $query->where('title', 'like', '%' . $search . '%')
                    ->orWhere('text', 'like', '%' . $search . '%');
            });
        }

        $articles->orderBy($sortColumn, $order);

        //Пагинация
        $articlesPaginated = $articles->paginate($limit, ['*'], 'page', $page);

        return $articlesPaginated;
    }

    public function generateUri(array $articleData): string
    {
        $uri = empty($articleData['title']) ? $articleData['name'] : $articleData['title'];
        return Helper::getUri($uri);
    }


}
