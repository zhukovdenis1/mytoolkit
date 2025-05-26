<?php

declare(strict_types=1);

namespace App\Modules\Shop\Services;
use App\Helpers\ShopArticleHelper;
use App\Helpers\ShopCouponHelper;
use App\Helpers\StringHelper;
use App\Models\MyIp;
use App\Modules\Shop\Models\ShopProduct;
use App\Modules\Shop\Models\ShopVisit;
use App\Modules\ShopArticle\Models\ShopArticle;
use App\Modules\ShopArticle\Services\Shared\ShopArticleService;
use App\Services\BaseService;
use Carbon\Carbon;
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
    ){}


    public function getArticleData(): array
    {
       return $this->articleHelper->getDataByCode('home');
    }

    public function getGoRedirectUrl(array $validated, Request $request): string
    {
        $goUrl = '/';
        $detector = new CrawlerDetect();

//        $agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
//        if (!(strpos($agent, 'Bot/') || strpos($agent, 'bot/'))) {
        if ($detector->isCrawler($request->header('User-Agent'))) {
            //Log::channel('bot')->info('Bot: ', $request->all());
            return $goUrl;
        }

        $url = $validated['url'] ?? '';
        $aliProductId = $validated['aid'] ?? 0;
        $search = $validated['search'] ?? '';
        //$title = $validated['title'] ?? '';
        $couponId = intval($validated['coupon_id'] ?? 0);

        $redirectUrl = 'https://aliexpress.ru/';

        if ($aliProductId) {
            $product = ShopProduct::query()->where('id_ae', $aliProductId)->first();
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
            } else {
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
            } else {
                $redirectUrl = 'https://aliexpress.ru/wholesale?SearchText=' . urlencode($search);
            }
        }

        //$ip = $_SERVER['REMOTE_ADDR'];

        //mysqli_query($resource, "INSERT INTO ali_product_redirect (site_id,ali_product_id,search_text,url,ip,agent) VALUES ( $siteId,'$aliProductId','$mysqlsText','$referer','$ip','$agent')");

        if ($redirectUrl && str_contains($redirectUrl, 'aliexpress.ru')) {
             //$goUrl = 'http://click.deshevyi.ru/redirect/cpa/o/sn6o728y02533c8wkahea3zoo0s0qodj/?erid=2SDnjdhZBWB&to=' . $redirectUrl;
             $goUrl = 'https://shopnow.pub/redirect/cpa/o/sn6o728y02533c8wkahea3zoo0s0qodj/?erid=2SDnjdhZBWB&to=' . urlencode($redirectUrl);
             //$goUrl = 'http://shopnow.pub/redirect/cpa/o/swacrnvh0ri1q4e1i5jitag6ro8w0uhf/?erid=2SDnjckbu97&to=' . $redirectUrl;//new link
        } else {
            Log::channel('daily')->warning('Переход без афилиатной ссылки: ', ['url' => $redirectUrl]);
            $goUrl = $redirectUrl;
        }


        return $goUrl;
    }

    public function incViews(Request $request, int $productId): void
    {
        $detector = new CrawlerDetect();

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
        }
    }

    public function getMainPageCoupons(): Collection
    {
        return $this->couponService->getMainPageCoupons();
    }

    public function getMainPageArticles(): Collection
    {
        return $this->articleService->getMainPageArticles();
    }

    public function registerVisit(array $session, ?string $sid, ?string $ip): bool
    {
        $itsMe = MyIp::where('ip', $ip)->exists();

        if ($itsMe) {
            return false;
        }

        $referrer = $session['referrer'] ?? null;

        $isExternal = $referrer ? !Str::contains($referrer, config('app.shop_url')) : null;

        $uri = $session['lastUri'];
        $userAgent = $session['userAgent'];

        if ($session['isBot'] ?? null) {
            Log::channel('bot_visits')->info('Bot: ', [
                'page_name' => $session['lastRoute']['page_name'] ?? null,
                'user_agent' => $userAgent ? Str::limit($userAgent, 255) : null,
                'sid' => $sid,
                'ip' => $ip,
                'uri' => $uri ? Str::limit($uri, 255) : null,
                'referrer' => $referrer ? Str::limit($referrer, 255) : null,
                'item_id' => $session['lastRoute']['item_id'] ?? null,
                'visit_num' => $session['visitNum'] ?? null,
                'is_bot' => $session['isBot'] ?? null,
                'is_mobile' => $session['isMobile'] ?? null,
                'is_external' => $isExternal,
            ]);
        } else {
            // Создаем запись о визите
            ShopVisit::create([
                'page_name' => $session['lastRoute']['page_name'] ?? null,
                'user_agent' => $userAgent ? Str::limit($userAgent, 255) : null,
                'sid' => $sid,
                'ip' => $ip,
                'uri' => $uri ? Str::limit($uri, 255) : null,
                'referrer' => $referrer ? Str::limit($referrer, 255) : null,
                'item_id' => $session['lastRoute']['item_id'] ?? null,
                'visit_num' => $session['visitNum'] ?? null,
                'is_bot' => $session['isBot'] ?? null,
                'is_mobile' => $session['isMobile'] ?? null,
                'is_external' => $isExternal,
                'created_at' => Carbon::now(),
            ]);
        }

        return true;
    }
}
