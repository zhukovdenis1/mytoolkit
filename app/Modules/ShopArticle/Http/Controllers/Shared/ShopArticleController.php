<?php

declare(strict_types=1);

namespace App\Modules\ShopArticle\Http\Controllers\Shared;

use App\Helpers\ShopArticleHelper;
use App\Http\Controllers\Controller;
use App\Modules\Shop\Models\ShopProduct;
use App\Modules\ShopArticle\Models\ShopArticle;
use App\Modules\ShopArticle\Services\Shared\ShopArticleService;
use Carbon\Carbon;
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

        $articles = $this->service->findPaginated($validated, app()->siteId());
        return view('Shop::shop.articles', [
            'articles' => $articles,
        ]);
    }

    public function detail(ShopArticle $article, string $articleHru='')
    {
        if ($article->site_id !== app()->siteId()) {
            abort(404);
        }

        if ($article->published_at > Carbon::now()) {
            abort(404);
        }

        if ($article->code && $article->product_id) {//редирект для обзоров т.к. они уже проиндексировались. Потом можно убрать это
            return redirect()->route('detail', ['product' => $article->product_id], 301);
        }

        if ($article->code) {
            abort(404);
        }

        if ($articleHru != $article->uri) {
            return redirect()->route('article.detail', ['article' => $article, 'articleHru' => $article->uri], 301);
        }

        return view('Shop::shop.article', [
//            'product' => $article->code && str_contains($article->code, 'review')
//                ? ShopProduct::select('id', 'id_ae', 'hru', 'video', 'characteristics', 'reviews')
//                    ->where('id' , str_replace('review-', '', $article->code))
//                    ->first()
//                : null,
            'product' => $article->product_id
                ? ShopProduct::select('id', 'id_ae', 'hru', 'video', 'characteristics', 'reviews')
                    ->where('id' , $article->product_id)
                    ->first()
                : null,
            'article' => $this->service->prepareForDisplay($article),
        ]);
    }
}
