<?php

declare(strict_types=1);

namespace App\Modules\ShopArticles\Http\Controllers\Admin;

use App\Exceptions\ErrorException;
use App\Http\Controllers\BaseController;
use App\Http\Resources\AnonymousResource;
use App\Modules\ShopArticles\Http\Requests\Admin\SearchShopArticleRequest;
use App\Modules\ShopArticles\Http\Requests\Admin\StoreShopArticleRequest;
use App\Modules\ShopArticles\Http\Requests\Admin\UpdateContentShopArticleRequest;
use App\Modules\ShopArticles\Http\Requests\Admin\UpdateShopArticleRequest;
use App\Modules\ShopArticles\Http\Resources\Admin\ShopArticleResource;
use App\Modules\ShopArticles\Http\Resources\Admin\ShopArticleResourceCollection;
use App\Modules\ShopArticles\Models\ShopArticle;
use App\Modules\ShopArticles\Services\ShopArticleService;


class ShopArticleController extends BaseController
{
    public function __construct(private readonly ShopArticleService $articleService) {}

    public function index(SearchShopArticleRequest $request): ShopArticleResourceCollection
    {
        $articles = $this->articleService->findPaginated(
            $request->validated()
        );
        return new ShopArticleResourceCollection($articles);
    }

    public function store(StoreShopArticleRequest $request): ShopArticleResource
    {
        $article = $this->articleService->create(
            $request->validated()
        );
        return new ShopArticleResource($article);
    }

    /**
     * @throws ErrorException
     */
    public function update(UpdateShopArticleRequest $request, ShopArticle $article): ShopArticleResource
    {
        $article = $this->articleService->update(
            $article,
            $request->validated()
        );
        return new ShopArticleResource($article);
    }

    /**
     * @throws ErrorException
     */
    public function updateContent(UpdateContentShopArticleRequest $request, ShopArticle $article): ShopArticleResource
    {
        $article = $this->articleService->updateContent(
            $request->validated(),
            $article
        );
        return new ShopArticleResource($article);
    }

    /**
     * @throws ErrorException
     */
    public function destroy(ShopArticle $article): AnonymousResource
    {
        return new AnonymousResource(["success" => $this->articleService->destroy($article)]);
    }
}
