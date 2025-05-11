<?php

declare(strict_types=1);

namespace App\Modules\Shop\Services;
use App\Helpers\ShopArticleHelper;
use App\Helpers\ShopCouponHelper;
use App\Helpers\StringHelper;
use App\Models\MyIp;
use App\Modules\Shop\Models\ShopProduct;
use App\Services\BaseService;
use Illuminate\Http\Request;
use Jaybizzle\CrawlerDetect\CrawlerDetect;
use Illuminate\Support\Facades\Log;

class ShopService extends BaseService
{
    public function __construct(
        private readonly ShopArticleHelper $articleHelper,
        private readonly ShopCouponHelper $couponHelper,
        private readonly StringHelper $stringHelper
    ){}


    public function getArticleData(): array
    {
       return $this->articleHelper->getDataByCode('home');
    }

    public function getGoRedirectUrl(array $validated, Request $request): string
    {
        $goUrl = '/';
        $detector = new CrawlerDetect();

        //$agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        //if (!(strpos($agent, 'Bot/') || strpos($agent, 'bot/'))) {
        if ($detector->isCrawler($request->header('User-Agent'))) {
            Log::channel('bot')->info('Bot: ', $request->all());
            return $goUrl;
        }

        $url = $validated['url'] ?? '';
        $aliProductId = $validated['aid'] ?? 0;
        $search = $validated['search'] ?? '';
        $title = $validated['title'] ?? '';
        $couponId = intval($validated['coupon_id'] ?? 0);

        $searchText = $search;
        if ($title) {
            $sTextArr = explode(' ', $title);
            $ssTextArr = [];
            for ($i = 0; $i < min(2, count($sTextArr)); $i++) {
                $ssTextArr[] = $sTextArr[$i];
            }
            $searchText = implode(' ', $ssTextArr);
            //временно
            $searchText = $this->stringHelper->transliterate($searchText);
        } elseif ($search) {
            //временно
            $searchText = $this->stringHelper->transliterate($search);
        }

        //$ip = $_SERVER['REMOTE_ADDR'];

        //mysqli_query($resource, "INSERT INTO ali_product_redirect (site_id,ali_product_id,search_text,url,ip,agent) VALUES ( $siteId,'$aliProductId','$mysqlsText','$referer','$ip','$agent')");

        $redirectUrl = null;

        if ($searchText == '{basket}') {
            $redirectUrl = 'https://aliexpress.ru/cart';
        } elseif ($searchText == '{wishlist}') {
            $redirectUrl = 'https://aliexpress.ru/wishlist';
        } elseif ($searchText == '{login}') {
            $redirectUrl = 'https://login.aliexpress.ru/';
        } elseif (!$aliProductId && $searchText) {
            //$redirectUrl = 'https://aliexpress.ru/w/wholesale-' . urlencode($searchText) . '.html';
            $redirectUrl = 'https://aliexpress.ru/wholesale?SearchText=' . urlencode($searchText);
        } elseif ($aliProductId) {
            //$redirectUrl = 'https://aliexpress.com/item/xxx/'. $aliProductId .'.html';
            $redirectUrl = 'https://aliexpress.ru/item/' . $aliProductId . '.html';
        } elseif ($url) {
            $redirectUrl = $url;
        } elseif ($couponId) {
            $redirectUrl = $this->couponHelper->getRedirectUrl($couponId);
        }

        if ($redirectUrl && str_contains($redirectUrl, 'aliexpress.ru')) {
             //$goUrl = 'https://shopnow.pub/redirect/cpa/o/sn6o728y02533c8wkahea3zoo0s0qodj/?erid=2SDnjdhZBWB&to=' . $redirectUrl;
             $goUrl = 'http://click.deshevyi.ru/redirect/cpa/o/sn6o728y02533c8wkahea3zoo0s0qodj/?erid=2SDnjdhZBWB&to=' . $redirectUrl;
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
                        ->withoutTimestamps()
                        ->increment('views');
                }
            }
        }
    }
}
