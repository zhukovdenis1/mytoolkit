<?php

declare(strict_types = 1);

namespace App\Console\Commands\Shop;

use App\Helpers\DeepSeekHelper;
use App\Helpers\StringHelper;
use App\Modules\Shop\Models\ShopProduct;
use App\Modules\Shop\Models\ShopReview;
use App\Modules\ShopArticle\Models\ShopArticle;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;


class GenArticlesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shop:genArticles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    public function __construct(
        private readonly StringHelper $stringHelper,
        private readonly DeepSeekHelper $deepSeekHelper
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $numArticles = 2;
        $symbolsAmount = 10000;
        $reviewsLimit = 100;

        $output = '';

        $product = ShopProduct::query()
            ->where('epn_month_income', '>', 2000)
            ->where('epn_cashback', '>', 200)
            ->whereNull('articles_created_at')
            ->where('reviews_amount', '>', 100)
            ->orderBy('epn_month_income', 'desc')
            ->first();

        if (!$product) {
            $this->info('no products for article');

            return 0;
        }

        $reviews = ShopReview::query()
            ->where('product_id', $product->id)
            ->where('grade', '>', 2)
            ->orderBy('sort')
            ->limit($reviewsLimit)
            ->get();

        $articles = ShopArticle::query()
            ->select(['site_id'])
            ->where('product_id', $product->id)
            ->where('code', 'like', 'review-%')
            ->limit($reviewsLimit)
            ->get()
            ->toArray();

        //var_dump(config('sites'));die;

        $nextSitesIds = (function(array $existsIdList, int $numArticles) {
            $sites = config('sites');
            $allIdList = [];
            foreach ($sites as $s) {
                if ($s['group'] == 'shop') {
                    $allIdList[] = $s['id'];
                }
            }
            $rest = array_diff($allIdList, $existsIdList);
            return array_slice($rest, 0, $numArticles);;
        })(array_column($articles, 'site_id'), $numArticles);

        $numArticles = count($nextSitesIds);

        if ($numArticles == 0) {
            $product->update(['articles_created_at' => Carbon::now()]);
            $this->info('All articles created for product_id=' . $product->id);
            return 0;
        }

        $message = $this->getMessage($product, $reviews, $symbolsAmount, $numArticles);

        //$dsResponse = [];
        $dsResponse = $this->deepSeekHelper->chat($message);

        //var_dump($dsResponse);

        $content = $dsResponse['choices'][0]['message']["content"] ?? null;

        //var_dump($content);
        $contentData = [];
        $content = str_replace('```json', '', $content);
        $content = str_replace('```', '', $content);
        $content = trim($content);

        try {
            if (empty($content)) {
                throw new \Exception('Empty content');
            }
            $contentData = json_decode($content, true);
//var_dump('contentData:', $contentData);
            if (empty($contentData) || !is_array($contentData)) {
                throw new \Exception('Not valid json');
            }

            if (count($contentData) < $numArticles) {
                throw new \Exception('Not enough articles generated');
            }

            Log::channel('deepseek_requests')->info('DeepSeek API Request:', [
                'count' => count($contentData),
                'request' => $message,
                'response' => $dsResponse,
            ]);


        } catch (\Exception $e) {
            $error = $e->getMessage();
            Log::channel('deepseek_errors')->error('DeepSeek API Error:', [
                'message' => $error,
                'request' => $message,
                'response' => $dsResponse,
            ]);
            $this->info('Error: ' . $error);
            return 0; // Возвращаем 0, если команда выполнена успешно
        }

        $i = 0;
        foreach ($contentData as $c) {
            $siteId = $nextSitesIds[$i];
            $i++;
            ShopArticle::create(
                [
                    'site_id' => $siteId,
                    'product_id' => $product->id,
                    'name' => $product->title_ae,
                    'h1' => $c['h1'] ?? null,
                    'title' => $c['title'] ?? null,
                    'keywords' => $c['keywords'] ?? null,
                    'description' => $c['description'] ?? null,
                    'uri' => $this->stringHelper->buildUri($c['h1'] ?: $product->title_ae),
                    'code' => 'review-' . $product->id,
                    'text' => [
                        [
                            'type' => 'product',
                            'data' => [
                                'text' => null,
                                'id' => $product->id,
                                'title' => $product->title_ae,
                                'props' => $c['props'] ?? null,
                                'cons' => $c['cons'] ?? null
                            ]
                        ],
                        [
                            'type' => 'visual',
                            'data' => [
                                'text' => $c['text']
                            ]
                        ]
                    ],
                    'note' => 'deepseek',
                    'published_at' => $this->genPublishDate($siteId)
                ]
            );
        }



        $output .= 'id = ' . $product->id . '; count =' . count($contentData) . ' ; title_ae=' . $product->title_ae
            . ' sites_id: ' . implode(',', $nextSitesIds) ;

        $this->info($output);
        return 0; // Возвращаем 0, если команда выполнена успешно
    }

    /**
     *
     * @param int $siteId - от 2 до 9
     * @return Carbon
     */
    private function genPublishDate(int $siteId): ?Carbon
    {
        if ($siteId == 2) {
            return Carbon::now();
        }

        if ($siteId == 8 || $siteId == 9) {
            return null;
        }

        // Для siteId 3-8 генерируем случайную дату в пределах +2 месяцев
        $now = Carbon::now();
        $endDate = $now->copy()->addMonths(2);

        // Разница в секундах между текущей датой и конечной датой
        $diffInSeconds = $now->diffInSeconds($endDate);

        // Генерируем случайное количество секунд в этом диапазоне
        $randomSeconds = rand(0, (int) $diffInSeconds);

        return $now->addSeconds($randomSeconds);
    }


    private function getMessage(ShopProduct $product, Collection $reviews, int $symbolsAmount, int $numArticles): string
    {
        $tagsText = '';
        if (isset($product->extra_data['reviews']['tags'])) {
            $tagsText = '---
        Данные о том что люди чаще всего упоминают в отзывх:
        ';
            foreach ($product->extra_data['reviews']['tags'] as $tag) {
                $tagsText .= $tag['title'] . ' - ' . $tag['counter'] . ' раз' . PHP_EOL;
            }
        }

        $reviewData = [];

        foreach ($reviews as $review) {
            $reviewData[] = [
                'reviewerName' => $review['reviewer']['name'],
                'date' => $review['date'],
                'grade' => $review['grade'],
                'likesAmount' => $review['likesAmount'],
                'text' => $review['text'],
                'additional' => $review['additional']
            ];
        }

        $reviewsText = json_encode($reviewData, JSON_UNESCAPED_UNICODE);

        $message = '

Напиши обзор на товар не используя других данных, кроме нижеприведенных. Основывайся в основном на отзывах.
Оптимизируй текст под низкочастотные запросы, чтобы обзор был в топе выдачи поисковиков по ним.
Оформи в виде статьи с заголовком, введением и тд. (не даелай ссылки в виде цифр, когда отзывы цитируешь). Не менее 6 подразделов. Каждый подраздел не менее 800 символов(букв)
Отдельно сформируй мета теги: title, description,keywords и h1 для страницы (ниже приведена структура, вставь их в соответствующие поля)
Старайся сделать текст уникальным для поисковиков. Тебе нужно будет составить ' . $numArticles . ' статьи(ю)(ей) на одну тему, но каждая из них должна быть уникальна для поисковиков. (Раньше вероятно уже были подбный запрос от меня на статью на даннюу тему, поэтому, если можно, тоже учитывай это и не генерируй дубликат т.е. не бери из кеша рузультат)
Также выдели кратко основные плюсы и минусы. 5-10 плюсов ($props) и 1-4 минуса ($cons) в параметрах props и cons стурутуры ответа
Цель статьи - продать товар, поэтому больше акцентируй внимание на достоинствах и поменьше выделяй недостатки и как-то смягчай их.

Текст статьи офомрляй точно так же как ты это делаешь в веб версии с подзаголовками, hr, utf-8 символами-картинками (очень желательна вставка таких utf-8 символов) и тд. Внимание, только в тексте статьи! В остальных местах без этого

Сруктура ответа для одного элемента (для одной статьи):

{
    "product_id": "$product_id"
    "name" : "$name",
    "h1": "$h1",
    "title": "$title",
    "description": "$description",
    "keywords": "$keywords",
    "props": "$props"
    "cons": "$cons"
    "text": "$text"
}

Итоговый ответ должен представлять собой json (json должен быть валидным - сбалансированные кавычки, экранирование и тд.) из ' . $numArticles . ' таких элементов: [{"product_id":"...", "name": "...",....},...,{...}]
Внимание ! В ответе в разделе message.content должен быть валидный Json без лишних слов ["choices"][0]["message"]["content"] = "[{"product_id":"...", "name": "...",....},...,{...}]"

$name - название товара, оно одно для всех: "' . $product->title_ae . '"
$product_id - id товара, одно для всех: ' . $product->id . '
Все остальные параметры индивидуальны для каждой статьи.
$h1 - h1 для страницы
$title, $description, $keyworks - мета теги в head.
$text - текст (html) самой статьи. Размер статьи должен быть не менее ' . $symbolsAmount . ' символов(букв). (Внимание. Это очень важно чтобы размер был не менее  ' . $symbolsAmount . ' символов)
$props - преимущества товара (просто текст, разделенный переводом строки)
$cons - недостатки товара (просто текст, разделенный переводом строки)

Входные данные:

$product_id: ' . $product->id . '
---
$name: ' . $product->title_ae . '
---
Характеристики:

' . $product->characteristics . '

' . $tagsText . '

---
Отзывы:

' . $reviewsText;

        return $message;
    }
}
