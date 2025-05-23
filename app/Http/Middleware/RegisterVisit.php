<?php

declare(strict_types = 1);

namespace App\Http\Middleware;

use App\Modules\Shop\Models\ShopVisit;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Jaybizzle\CrawlerDetect\CrawlerDetect;
use Symfony\Component\HttpFoundation\Response;
use App\Models\MyIp;

class RegisterVisit
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $sid = $request->cookie('sid') ?? bin2hex(random_bytes(8));
        $ip = $request->ip();
        $itsMe = MyIp::where('ip', $ip)->exists();

        $response = $next($request);

        // Проверяем, есть ли уже cookie
        if (!$request->hasCookie('sid')) {

            // Генерируем уникальный ID
            //$sid = /*Str::uuid(); // или*/ bin2hex(random_bytes(8));

            // Создаем cookie на 5 лет
            $response->headers->setCookie(cookie('sid', $sid, 2628000));
//            $cookie = cookie('sid', $sid, 2628000);
//            $response = $next($request);
//            return $response->cookie($cookie);
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
        $isBot = $crawlerDetect->isCrawler($request->userAgent());
        $isMobile = $this->isMobile($request->userAgent());
        $referrer = $request->header('referer');
        $isExternal = $referrer && !Str::contains($referrer, env('APP_SHOP_URL'));
        $itemInfo = $this->getItemInfoFromRoute($request);

        if ($isBot) {
            Log::channel('bot_visits')->info('Bot: ', [
                'page_name' => $this->getPageName($request),
                'user_agent' => Str::limit($request->userAgent(), 255),
                'sid' => $sid,
                'ip' => $ip,
                'uri' => Str::limit($request->path(), 255),
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
                'uri' => Str::limit($request->path(), 255),
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
    }

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

        return ['id' => 0];
    }

}
