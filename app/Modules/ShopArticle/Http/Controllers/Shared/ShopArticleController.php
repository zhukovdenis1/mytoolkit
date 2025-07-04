<?php

declare(strict_types=1);

namespace App\Modules\ShopArticle\Http\Controllers\Shared;

use App\Helpers\ShopArticleHelper;
use App\Http\Controllers\Controller;
use App\Modules\Shop\Models\ShopProduct;
use App\Modules\ShopArticle\Models\ShopArticle;
use App\Modules\ShopArticle\Services\Shared\ShopArticleService;
use Illuminate\Http\Request;

class ShopArticleController extends Controller
{
    public function __construct(
        private readonly ShopArticleService $service,
    ) {}
    public function index(Request $request)
    {
        $validated = $request->validate([
            'search'        => ['nullable', 'string', 'min:1', 'max:100'],
            'page'        => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $articles = $this->service->findPaginated($validated);
        return view('Shop::shop.articles', [
            'articles' => $articles,
        ]);
    }

    public function detail(ShopArticle $article, string $articleHru='')
    {
        if ($articleHru != $article->uri) {
            return redirect()->route('article.detail', ['article' => $article, 'articleHru' => $article->uri], 301);
        }

        return view('Shop::shop.article', [
            'product' => $article->code && str_contains($article->code, 'review')
                ? ShopProduct::select('id', 'id_ae', 'hru', 'video', 'characteristics', 'reviews')
                    ->where('id' , str_replace('review-', '', $article->code))
                    ->first()
                : null,
            'article' => $this->service->prepareForDisplay($article),
        ]);
    }
}
