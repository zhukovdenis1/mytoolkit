<?php

declare(strict_types=1);

namespace App\Modules\Shop\Services;
use App\Helpers\ShopArticleHelper;
use App\Helpers\ShopCouponHelper;
use App\Helpers\StringHelper;
use App\Logging\AppLogger;
use App\Models\MyIp;
use App\Modules\Shop\Models\ShopProduct;
use App\Modules\Shop\Models\ShopVisit;
use App\Modules\ShopArticle\Models\ShopArticle;
use App\Modules\ShopArticle\Services\Shared\ShopArticleService;
use App\Services\BaseService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Jaybizzle\CrawlerDetect\CrawlerDetect;
use Illuminate\Support\Facades\Log;

class ShopService extends BaseService
{
    public function __construct(
        private readonly ShopArticleHelper $articleHelper,
        private readonly ShopCouponHelper $couponHelper,
        //private readonly StringHelper $stringHelper,
        private readonly ShopCouponService $couponService,
        private readonly ShopArticleService $articleService,
        private readonly AppLogger $appLogger
    ){}


    public function getArticleData($code): ?array
    {
       return $this->articleHelper->getDataByCode($code);
    }

    public function getGoRedirectUrl(array $validated, Request $request): string
    {
        $goUrl = '/';
        $detector = new CrawlerDetect();

//        $agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
//        if (!(strpos($agent, 'Bot/') || strpos($agent, 'bot/'))) {
        if ($detector->isCrawler($request->header('User-Agent'))) {
            return $goUrl;
        }

        $url = $validated['url'] ?? '';
        $aliProductId = $validated['aid'] ?? 0;
        $productId = $validated['id'] ?? 0;
        $search = $validated['search'] ?? '';
        $pageName = $validated['page_name'] ?? null;
        //$title = $validated['title'] ?? '';
        $couponId = intval($validated['coupon_id'] ?? 0);

        $redirectUrl = 'https://aliexpress.ru/';

        if ($productId || $aliProductId) {
            if ($productId) {
                $product = ShopProduct::query()->where('id', $productId)->first();
            } else {
                $product = ShopProduct::query()->where('id_ae', $aliProductId)->first();
            }

            if ($product && $product->not_found_at) {
                $categoryId = $product->category_id;
                if ($categoryId) {
                    $redirectUrl = 'https://aliexpress.ru/category/' . $categoryId . '/x';
                } else {
                    $title = $product->title ?? $product->title_ae;
                    $sTextArr = explode(' ', $title);
                    $ssTextArr = [];
                    for ($i = 0; $i < min(4, count($sTextArr)); $i++) {
                        $ssTextArr[] = $sTextArr[$i];
                    }
                    $searchText = implode(' ', $ssTextArr);
                    //временно
                    //$searchText = $this->stringHelper->transliterate($searchText);
                    $redirectUrl = 'https://aliexpress.ru/wholesale?SearchText=' . urlencode($searchText);
                }
            } elseif ($product) {
                if ($pageName == 'reviews') {
                    $redirectUrl = 'https://aliexpress.ru/item/' . $product->id_ae . '/reviews';
                } else {
                    $redirectUrl = 'https://aliexpress.ru/item/' . $product->id_ae . '.html';
                }
            } elseif ($aliProductId) {
                $redirectUrl = 'https://aliexpress.ru/item/' . $aliProductId . '.html';
            }
        } elseif ($couponId) {
            $redirectUrl = $this->couponHelper->getRedirectUrl($couponId) ?? 'https://aliexpress.ru';
        } elseif ($url) {
            $redirectUrl = $url;
        } elseif ($search) {
            if ($search == '{basket}') {
                $redirectUrl = 'https://aliexpress.ru/cart';
            } elseif ($search == '{wishlist}') {
                $redirectUrl = 'https://aliexpress.ru/wishlist';
            } elseif ($search == '{login}') {
                $redirectUrl = 'https://login.aliexpress.ru/';
            } else {//здесь могут быть так же h1 из oldDetail
                $sTextArr = explode(' ', trim($search));
                $ssTextArr = array();
                for ($i = 0; $i < 5; $i++) $ssTextArr[] = $sTextArr[$i];
                $sText = implode(' ', $ssTextArr);
                $redirectUrl = 'https://aliexpress.ru/wholesale?SearchText=' . urlencode($sText);
            }
        }

        //$ip = $_SERVER['REMOTE_ADDR'];

        //mysqli_query($resource, "INSERT INTO ali_product_redirect (site_id,ali_product_id,search_text,url,ip,agent) VALUES ( $siteId,'$aliProductId','$mysqlsText','$referer','$ip','$agent')");

        if ($redirectUrl && str_contains($redirectUrl, 'aliexpress.ru')) {
             //$goUrl = 'https://click.deshevyi.ru/redirect/cpa/o/sn6o728y02533c8wkahea3zoo0s0qodj/?erid=2SDnjdhZBWB&to=' . $redirectUrl;
             $goUrl = 'https://shopnow.pub/redirect/cpa/o/sn6o728y02533c8wkahea3zoo0s0qodj/?erid=2SDnjdhZBWB&to=' . urlencode($redirectUrl);
             //$goUrl = 'http://shopnow.pub/redirect/cpa/o/swacrnvh0ri1q4e1i5jitag6ro8w0uhf/?erid=2SDnjckbu97&to=' . $redirectUrl;//new link
        } else {
            $this->appLogger->critical('Переход без афилиатной ссылки', ['redirectUrl' => $redirectUrl]);
            $goUrl = $redirectUrl;
        }

        return $goUrl;
    }

    public function incViews(Request $request, int $productId): void
    {
        /*$detector = new CrawlerDetect();

        if (!$detector->isCrawler($request->header('User-Agent'))) {
            $ip = $request->ip();
            if ($ip && $ip != '127.0.0.1') {
                $ipExists = MyIp::where('ip', $ip)->exists();
                if (!$ipExists) {
                    ShopProduct::where('id', $productId)
                        ->update([
                            'views' => DB::raw('views + 1')
                        ]);
                }
            }
        }*/
    }

    public function getMainPageCoupons(): Collection
    {
        return $this->couponService->getMainPageCoupons();
    }

    public function getMainPageArticles(): Collection
    {
        return $this->articleService->getMainPageArticles();
    }

    public function registerVisit(array $session, ?string $sid, ?string $ip, ?string $goref): bool
    {
        $itsMe = MyIp::where('ip', $ip)->exists();

        if ($itsMe) {
            return false;
        }

        $uri = $session['lastUri'];
        $referrer = $session['referrer'] ?? null;
        $pageName = $session['lastRoute']['page_name'] ?? null;
        $itemId = $session['lastRoute']['item_id'] ?? null;
        $isExternal = $referrer ? !Str::contains($referrer, config('app.shop_url')) : null;
        $visitNum = $session['visitNum'] ?? null;

        if ($goref) {
            $goParams = (function() use ($goref) {
                // Здесь может быть любая логика
                if (!$goref) return [];
                $parsedUrl = parse_url($goref);
                $query = $parsedUrl['query'] ?? '';
                parse_str($query, $params);
                return $params;
            })();
            $pageName = 'go';
            $referrer = $uri;
            $uri = (function() use ($goref) {
                $parsed = parse_url($goref);
                $path = $parsed['path'] ?? '';
                $query = $parsed['query'] ?? '';
                return $path . ($query ? '?' . $query : '');
            })();
            $itemId = $goParams['id'] ?? null;
            $isExternal = 0;
            $visitNum++;
        }

        $userAgent = $session['userAgent'];

        if ($session['isBot'] ?? null) {
            Log::channel('bot_visits')->info('Bot: ', [
                'page_name' => $pageName,
                'user_agent' => $userAgent ? Str::limit($userAgent, 255) : null,
                'sid' => $sid,
                'ip' => $ip,
                'uri' => $uri ? Str::limit($uri, 255) : null,
                'referrer' => $referrer ? Str::limit($referrer, 255) : null,
                'item_id' => $itemId,
                'visit_num' => $visitNum,
                'is_bot' => $session['isBot'] ?? null,
                'is_mobile' => $session['isMobile'] ?? null,
                'is_external' => $isExternal,
            ]);
        } else {
            // Создаем запись о визите
            ShopVisit::create([
                'page_name' => $pageName,
                'user_agent' => $userAgent ? Str::limit($userAgent, 255) : null,
                'sid' => $sid,
                'ip' => $ip,
                'ip_address' => $ip,
                'uri' => $uri ? Str::limit($uri, 255) : null,
                'referrer' => $referrer ? Str::limit($referrer, 255) : null,
                'item_id' => $itemId,
                'visit_num' => $visitNum,
                'is_bot' => $session['isBot'] ?? null,
                'is_mobile' => $session['isMobile'] ?? null,
                'is_external' => $isExternal,
                'created_at' => Carbon::now(),
            ]);
        }

        return true;
    }

    public function getEpnMenu(): array
    {
        $data = config('epn.categories');
        return array_filter($data, function($v, $k) {
            return (!empty($v['uri']) && !empty($v['active']));
        }, ARRAY_FILTER_USE_BOTH);

    }

    public function filter(int $page = 1, int $category = 0, string $search = '', int $epnCategory = 0): Collection
    {
        $limit = 48;
        $offset = $limit * ($page - 1);

        $query = ShopProduct::query()
            ->select('id', 'title', 'title_ae', 'photo', 'rating', 'price', 'price_from', 'price_to', 'hru', 'category_0')
            //->whereNull('deleted_at') soft delete автоматически это делает
            //->whereNull('not_found_at')
            //->whereNotIn('category_0', [16002,1309])
            ->orderBy('id', 'desc')
            ->offset($offset)
            ->limit($limit);

        if ($search) {
            //$query->where('title_ae', 'like', "%{$search}%");

            $query->where(function (Builder $query) use ($search) {
                $query
                    ->orWhere('title_ae', 'like', "%{$search}%")
                    ->orWhere('title_source', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ;
            });
        }

        if ($category) {
            //$query->where('category_id', $category);

            $query->where(function (Builder $query) use ($category) {
                $query->orWhere('category_id', $category)
                    ->orWhere('category_0', $category)
                    ->orWhere('category_1', $category)
                    ->orWhere('category_2', $category)
                    ->orWhere('category_3', $category);
            });
        }

        if ($epnCategory) {
            $query->where('epn_category_id', $epnCategory);
            $query->orderBy('epn_month_income', 'desc');
        }

        return $query->get();
    }

    public function getPopular(): Collection
    {
        $query = ShopProduct::query()
            ->select('id', 'title', 'title_ae', 'photo', 'rating', 'price', 'price_from', 'price_to', 'hru', 'category_0')
            //->whereNull('not_found_at')
            ->orderBy('epn_month_income', 'desc')
            ->limit(24);

        return $query->get();
    }

    public function prepareArticleForDisplay(ShopArticle $article): ShopArticle
    {
        return $this->articleService->prepareForDisplay($article);
    }
}
