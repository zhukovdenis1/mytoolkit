<?php

declare(strict_types=1);

namespace App\Modules\ShopArticle\Http\Controllers\Admin;

use App\Exceptions\ErrorException;
use App\Http\Controllers\BaseController;
use App\Http\Resources\AnonymousResource;
use App\Modules\FileStorage\Http\Requests\StoreFileRequest;
use App\Modules\FileStorage\Services\FileStorageService;
use App\Modules\ShopArticle\Http\Requests\Admin\SearchShopArticleRequest;
use App\Modules\ShopArticle\Http\Requests\Admin\StoreShopArticleRequest;
use App\Modules\ShopArticle\Http\Requests\Admin\UpdateContentShopArticleRequest;
use App\Modules\ShopArticle\Http\Requests\Admin\UpdateShopArticleRequest;
use App\Modules\ShopArticle\Http\Resources\Admin\ShopArticleResource;
use App\Modules\ShopArticle\Http\Resources\Admin\ShopArticleResourceCollection;
use App\Modules\ShopArticle\Models\ShopArticle;
use App\Modules\ShopArticle\Services\Admin\ShopArticleService;


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

    public function show(ShopArticle $article): ShopArticleResource
    {
        return new ShopArticleResource($article);
    }


    public function update(UpdateShopArticleRequest $request, ShopArticle $article): ShopArticleResource
    {
        $article = $this->articleService->update(
            $article,
            $request->validated()
        );
        return new ShopArticleResource($article);
    }


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

    /**
     * @throws ErrorException
     */
    public function storeFile(StoreFileRequest $request, ShopArticle $article, FileStorageService $service): AnonymousResource
    {
        return new AnonymousResource($service->saveByRequest($request, (int)$article->id, 'shop_article', false));
    }
}
