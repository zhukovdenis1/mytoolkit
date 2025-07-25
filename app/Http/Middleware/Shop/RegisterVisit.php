<?php

declare(strict_types = 1);

namespace App\Http\Middleware\Shop;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Jaybizzle\CrawlerDetect\CrawlerDetect;
use SebastianBergmann\CodeUnit\Exception;
use Symfony\Component\HttpFoundation\Response;

class RegisterVisit
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $response = $next($request);

        try {
            if (!$request->ajax()) {
                if (!$request->hasCookie('sid')) {
                    $sid = bin2hex(random_bytes(8));
                    // Создаем cookie на 5 лет
                    $response->headers->setCookie(cookie('sid', $sid, 2628000));
                }

                $userAgent = $request->userAgent() ?? null;
                // Управление счетчиком визитов через сессию
                $visitNum = $request->session()->get('visitNum', 0);
                $visitNum++;
                $request->session()->put('visitNum', $visitNum);
                $tid = $request->tid ?? null;

                if ((int)$tid) {
                    $request->session()->put('tid', (int)$tid);
                }

                if (!session()->has('userAgent')) {
                    $request->session()->put('userAgent', $userAgent);
                }

                $request->session()->put('referrer', $request->header('referer'));

                if (!session()->has('isMobile')) {
                    $request->session()->put('isMobile', $this->isMobile($userAgent));
                }

                if (!session()->has('isBot')) {
                    $crawlerDetect = new CrawlerDetect();
                    $isBot = $crawlerDetect->isCrawler($userAgent);
                    $request->session()->put('isBot', $isBot);
                }

                $uri = $request->getRequestUri();
                $request->session()->put('lastUri', $uri);

                $itemInfo = $this->getItemInfoFromRoute($request);
                $request->session()->put('lastRoute', [
                    'page_name' => $this->getPageName($request),
                    'item_id' => $itemInfo['id'],
                ]);

            }
        } catch (Exception $e) {

        }



        return $response;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
/*    public function handleOld(Request $request, Closure $next): Response
    {
        $sid = $request->cookie('sid') ?? bin2hex(random_bytes(8));
        $ip = $request->ip();
        $itsMe = MyIp::where('ip', $ip)->exists();

        $response = $next($request);

        // Проверяем, есть ли уже cookie
        if (!$request->hasCookie('sid')) {
            // Создаем cookie на 5 лет
            $response->headers->setCookie(cookie('sid', $sid, 2628000));
        }

        if ($itsMe) {
            return $response;
        }

        // Инициализация детектора ботов
        $crawlerDetect = new CrawlerDetect();

        // Управление счетчиком визитов через сессию
        $visitNum = $request->session()->get('visit_num', 0);
        $visitNum++;
        $request->session()->put('visit_num', $visitNum);

        // Определяем параметры визита
        $userAgent = $request->userAgent() ?? null;
        $isBot = $crawlerDetect->isCrawler($userAgent);
        $isMobile = $this->isMobile($userAgent);
        $referrer = $request->header('referer');
        //var_dump($referrer);var_dump(env('APP_SHOP_URL'));die;
        $isExternal = $referrer ? !Str::contains($referrer, config('app.shop_url')) : null;
        $itemInfo = $this->getItemInfoFromRoute($request);
        $uri = Str::limit($request->getRequestUri(), 255);

        if ($isBot) {
            Log::channel('bot_visits')->info('Bot: ', [
                'page_name' => $this->getPageName($request),
                'user_agent' => Str::limit($request->userAgent(), 255),
                'sid' => $sid,
                'ip' => $ip,
                'uri' => $uri,
                'referrer' => $referrer ? Str::limit($referrer, 255) : null,
                'item_id' => $itemInfo['id'],
                'visit_num' => $visitNum,
                'is_bot' => $isBot,
                'is_mobile' => $isMobile,
                'is_external' => $isExternal,
            ]);
        } else {
            // Создаем запись о визите
            ShopVisit::create([
                'sid' => $sid,
                'ip' => $ip,
                'user_agent' => Str::limit($request->userAgent(), 255),
                'referrer' => $referrer ? Str::limit($referrer, 255) : null,
                'uri' => $uri,
                'page_name' => $this->getPageName($request),
                'item_id' => $itemInfo['id'],
                'visit_num' => $visitNum,
                'is_bot' => $isBot,
                'is_mobile' => $isMobile,
                'is_external' => $isExternal,
                'created_at' => Carbon::now(),
            ]);
        }

        return $response;
    }*/

    protected function isMobile(?string $userAgent): bool
    {
        if (!$userAgent) {
            return false;
        }

        return preg_match(
                '/Mobile|Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i',
                $userAgent
            ) === 1;
    }

    protected function getPageName(Request $request): ?string
    {
        $route = $request->route();
        return $route ? Str::limit($route->getName(), 32) : null;
    }

    protected function getItemInfoFromRoute(Request $request): array
    {
        $route = $request->route();

        if (!$route) {
            return ['id' => null];
        }

        // Для маршрута article.detail
        if ($route->named('article.detail')) {
            return [
                'id' => (int) $route->parameter('article')->id,
            ];
        }

        // Для маршрута detail (product)
        if ($route->named('detail')) {
            return [
                'id' => (int) $route->parameter('product')->id,
            ];
        }

        if ($route->named('category')) {
            return [
                'id' => (int) $route->parameter('category')->id_ae,
            ];
        }

        return ['id' => 0];
    }

}
