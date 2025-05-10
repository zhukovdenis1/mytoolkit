<?php

declare(strict_types=1);

namespace App\Modules\Shop\Services;
use App\Helpers\ShopArticleHelper;
use App\Helpers\ShopCouponHelper;
use App\Services\BaseService;
use Illuminate\Http\Request;
use Jaybizzle\CrawlerDetect\CrawlerDetect;
use Illuminate\Support\Facades\Log;

class ShopService extends BaseService
{
    public function __construct(
        private readonly ShopArticleHelper $articleHelper,
        private readonly ShopCouponHelper $couponHelper,
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
        $searchText = $validated['search'] ?? '';
        $couponId = (int) $validated['coupon_id'] ?? 0;

        $sText = '';
        if ($searchText && $searchText[0] !== '{') {
            $sTextArr = explode(' ', $searchText);
            $ssTextArr = [];
            for ($i = 0; $i < 4; $i++) $ssTextArr[] = $sTextArr[$i];
            $sText = implode('-', $ssTextArr);
        } elseif ($searchText && $searchText[0] == '{') {
            $sText = $searchText;
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
        } elseif (!$aliProductId && $sText) {
            //$redirectUrl = urlencode('http://aliexpress.com/wholesale?SearchText=' . $sText);
            //$redirectUrl = urlencode('https://aliexpress.ru/wholesale?SearchText=' . $sText);
            $redirectUrl = urlencode('https://aliexpress.ru/w/wholesale-' . $sText . '.html');
        } elseif ($aliProductId) {
            //$redirectUrl = 'https://aliexpress.com/item/xxx/'. $aliProductId .'.html';
            $redirectUrl = 'https://aliexpress.ru/item/' . $aliProductId . '.html';
        } elseif ($url) {
            $redirectUrl = $url;
        } elseif ($couponId) {
            $redirectUrl = $this->couponHelper->getRedirectUrl($couponId);
        }

        if ($redirectUrl && str_contains($redirectUrl, 'aliexpress.ru')) {
            //header('Location: ' . 'https://shopnow.pub/redirect/cpa/o/sn6o728y02533c8wkahea3zoo0s0qodj/?erid=2SDnjdhZBWB&to=' . $redirectUrl);//krutye-veshi link EPN - d-x.su
            $goUrl = 'http://click.deshevyi.ru/redirect/cpa/o/sn6o728y02533c8wkahea3zoo0s0qodj/?erid=2SDnjdhZBWB&to=' . $redirectUrl;
        } else {
            Log::channel('daily')->warning('Переход без афилиатной ссылки: ', ['url' => $redirectUrl]);
            $goUrl = $redirectUrl;
        }


        return $goUrl;
    }
}
