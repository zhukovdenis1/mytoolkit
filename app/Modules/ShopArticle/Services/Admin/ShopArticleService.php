<?php

declare(strict_types=1);

namespace App\Modules\ShopArticle\Services\Admin;

use App\Exceptions\ErrorException;
use App\Helpers\Helper;
use App\Helpers\StringHelper;
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
        $article =  ShopArticle::create($validatedData);
        $this->addProductsToParseQueue($validatedData['text'] ?? '', $article->id);
        return $article;
    }

    public function update(BaseModel $model, array $attributes): BaseModel
    {
        $this->addProductsToParseQueue($attributes['text'] ?? '', $model->id);
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
        $uri = empty($articleData['title']) ? $articleData['h1'] : $articleData['title'];
        return $this->stringHelper->buildUri($uri);
    }


}
