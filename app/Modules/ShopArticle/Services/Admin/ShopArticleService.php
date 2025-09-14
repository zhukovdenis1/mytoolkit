<?php

declare(strict_types=1);

namespace App\Modules\ShopArticle\Services\Admin;

use App\Exceptions\ErrorException;
use App\Helpers\Helper;
use App\Helpers\StringHelper;
use App\Modules\Shop\Models\ShopProduct;
use App\Modules\ShopArticle\Models\ShopArticle;
use App\Services\BaseService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use App\Models\BaseModel;
use Illuminate\Support\Facades\DB;

class ShopArticleService extends BaseService
{
    public function __construct(private readonly StringHelper $stringHelper) {}
    public function create(array $validatedData): ShopArticle
    {
        $validatedData['uri'] = $this->generateUri($validatedData);
        if (is_string($validatedData['text'])) {
            $validatedData['text'] = json_decode($validatedData['text'], true);
        }
        $validatedData['published_at'] = $validatedData['published_at']
            ? Carbon::parse($validatedData['published_at'])
            : null;
        $article =  ShopArticle::create($validatedData);
        $this->addProductsToParseQueue($validatedData['text'] ?? '', $article->id);
        return $article;
    }

    public function update(BaseModel $model, array $attributes): BaseModel
    {
        $this->addProductsToParseQueue($attributes['text'] ?? '', $model->id);
        if (is_string($attributes['text'])) {
            $attributes['text'] = json_decode($attributes['text'], true);
        }
        $attributes['published_at'] = $attributes['published_at']
            ? Carbon::parse($attributes['published_at'])
            : null;
        return parent::update($model, $attributes);
    }

    private function addProductsToParseQueue(string $json, int $articleId): void
    {
        try {
            $data = json_decode($json, true);

        } catch (ErrorException $e) {
            $data = [];
        }
        if ($data) {
            $queue = [];
            foreach ($data as $d) {
                if (($d['type'] == 'product') && !empty($d['data']['id_ae'])) {
                    $queue[] = [
                        'important' =>1,
                        'id_ae' => $d['data']['id_ae'],
                        'info' => json_encode([
                            'articleId' => $articleId,
                        ], JSON_UNESCAPED_UNICODE),
                        'created_at' => Carbon::now(),
                    ];
                }
            }

            foreach (array_chunk($queue, 50) as $chunk) {
                DB::table('shop_products_parse_queue')->insertOrIgnore($chunk);
            }
        }
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
        $articles = ShopArticle::select(['id', 'site_id', 'product_id', 'name']);

        $search = $validatedData['search'] ?? null;
        $productId = $validatedData['product_id'] ?? null;
        $siteId = $validatedData['site_id'] ?? null;
        $page = empty($validatedData['_page']) ? 1 : intval($validatedData['_page']);
        $limit = empty($validatedData['_limit']) ? 20 : intval($validatedData['_limit']);
        $sortColumn = $validatedData['_sort'] ?? 'id';
        $order = $validatedData['_order'] ?? 'desc';
        $published = $validatedData['published'] ?? null;


        if ($search) {
            $articles->where(function (Builder $query) use ($search) {
                $query->where('title', 'like', '%' . $search . '%')
                    ->orWhere('text', 'like', '%' . $search . '%');
            });
        }

        if ($productId) {
            $articles->where('product_id', $productId);
        }

        if ($siteId) {
            $articles->where('site_id', $siteId);
        }

        if (!is_null($published)) {
            if ($published) {
                $articles->whereNotNull('published_at');
            } else {
                $articles->whereNull('published_at');
            }
        }

        $articles->orderBy($sortColumn, $order);

        //Пагинация
        $articlesPaginated = $articles->paginate($limit, ['*'], 'page', $page);

        return $articlesPaginated;
    }

    public function generateUri(array $articleData): string
    {
        $uri = empty($articleData['title']) ? $articleData['h1'] : $articleData['title'];
        return $this->stringHelper->buildUri($uri);
    }

    public function pubInfo(ShopArticle $article): string
    {
        $text = '';
        if ($article->site_id == 8 || $article->site_id == 9) {
            $product = ShopProduct::query()->find($article->product_id);
            if (!$product) {
                return $text;
            }
            $tid = '';
            if ($article->site_id == 8) {
                $text .= '<p>open in <b>firefox</b></p>';
                $tid = '?tid=3';
            }
            if ($article->site_id == 9) {
                $text .= '<p>open in <b>google</b></p>';
                $tid = '?tid=4';
            }
            $href = config('app.shop_scheme') . config('app.shop_url') . "/p-{$product->id}/{$product->hru}{$tid}";

            $text .= '<p><a href="' . $href . '" target="_blank">Более подробная информация: фото, видео, отзывы, характеристики... доступна по этой ссылке</a></p>';
            $text .= "<p><a href='$href' target='_blank'>$href</a></p>";
            $text .= '<p><a href="https://aliexpress.ru/item/' . $product->id_ae . '/reviews" target="_blank">Aliexpess</a></p>';
            $text .= '<p>';
            foreach ($product->photo as $p) {
                $text .= '<a target="blank" href="' . $p . '"><img src="' . $p . '" height="120px" /></a> ';
            }
            $text .= '</p>';

        }
        return $text;
    }

    public function prepareForDzen(ShopArticle $article): ShopArticle
    {
        if (in_array($article->site_id, [8,9]) && isset($article->text[0]) && isset($article->text[1])) {
            $product = ShopProduct::query()->find($article->product_id);
            if ($article->site_id == 8) {
                $tid = '?tid=3';
            }
            if ($article->site_id == 9) {
                $tid = '?tid=4';
            }
            $href = config('app.shop_scheme') . config('app.shop_url') . "/p-{$product->id}/{$product->hru}{$tid}";
            $text = $article->text;
            if ($text[0]['type'] == 'product' && $text[1]['type'] == 'visual') {
                $props = explode("\n", $text[0]['data']['props'] ?? []);
                $cons = explode("\n", $text[0]['data']['cons'] ?? []);
                $propsConsText = '<p>Основные преимущества и недостатки, которые отмечают покупатели в отзывах:</p>';
                $propsConsText .= '<p><b>Преимущества</b>:</p>';
                $propsConsText .= '<ul>';
                foreach ($props as $p) {
                    $propsConsText .= '<li>' . $p . '</li>';
                }
                $propsConsText .= '</ul>';
                $propsConsText .= '<p><b>Недостатки</b>:</p>';
                $propsConsText .= '<ul>';
                foreach ($cons as $c) {
                    $propsConsText .= '<li>' . $c . '</li>';
                }
                $propsConsText .= '</ul>';

                $text[1]['data']['text'] = $propsConsText . "<p><a href='$href' target='_blank'>$href</a></p>" . $text[1]['data']['text'];
                $text[1]['data']['text'] .= '<p><a href="' . $href . '" target="_blank">Более подробная информация: фото, видео, отзывы, характеристики... доступна по этой ссылке</a></p>';
                unset($text[0]);
                $article->text = $text;
            }
        }
        return $article;
    }

}
