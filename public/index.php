<?php

use Illuminate\Http\Request;

if (empty($_SERVER['HTTPS'])) {
    $redirectUrl = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header('HTTP/1.1 301 Moved Permanently');
    header('Location: ' . $redirectUrl);
    exit();
}

if(true || $_SERVER['HTTP_HOST'] == 'deshevyi.ru')
{
    /* ADMIN REDIRECT */
    $redirect = isset($_GET['r']) ? intval($_GET['r']) : 0;
    $agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $referer = $_SERVER['HTTP_REFERER'] ?? '';

    if ($redirect && !(strpos($agent, 'Bot/') || strpos($agent, 'bot/'))) {
        $siteId = $redirect;
        $aliProductId = isset($_GET['aid']) ? intval($_GET['aid']) : 0;
        $searchText = isset($_GET['st']) ? $_GET['st'] : '';

        $domainName = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);

        $sTextReserved = false;
        $sText = '';
        if ($searchText && $searchText[0] !== '{') {
            $sTextArr = explode(' ', $searchText);
            $ssTextArr = array();
            for ($i = 0; $i < 4; $i++) $ssTextArr[] = $sTextArr[$i];
            $sText = implode('-', $ssTextArr);
        } elseif ($searchText[0] == '{') {
            $sText = $searchText;
        }

        $ip = $_SERVER['REMOTE_ADDR'];


        //if ($_SERVER['REMOTE_ADDR'] == '92.39.219.112')
        {
            //file_get_contents('http://api.deshevii.ru/home/redirect_register?aliProductId='.$aliProductId.'&siteId='.$siteId.'&sText='.urlencode($sText).'&ip='.urlencode($ip).'&referer='.urlencode($referer).'&agent='.urlencode($agent));
            /*require_once(ROOT_DIR . '../admin.deshevii.ru/Api.php');
            $api = new Api();
            $api->aliProductRedirectRegister($aliProductId, $siteId, $sText, $ip, $agent, $referer);*/
        }
        //mysqli_query($resource, "INSERT INTO ali_product_redirect (site_id,ali_product_id,search_text,url,ip,agent) VALUES ( $siteId,'$aliProductId','$mysqlsText','$referer','$ip','$agent')");


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
        } else {
            //$redirectUrl = 'https://aliexpress.com/item/xxx/'. $aliProductId .'.html';
            $redirectUrl = 'https://aliexpress.ru/item/' . $aliProductId . '.html';
        }

        //header('Location: ' . 'https://shopnow.pub/redirect/cpa/o/sn6o728y02533c8wkahea3zoo0s0qodj/?erid=2SDnjdhZBWB&to=' . $redirectUrl);//krutye-veshi link EPN - d-x.su
        header('Location: ' . 'http://click.deshevyi.ru/redirect/cpa/o/sn6o728y02533c8wkahea3zoo0s0qodj/?erid=2SDnjdhZBWB&to=' . $redirectUrl);//krutye-veshi link EPN - d-x.su


        //header('Location: ' . 'https://alitems.site/g/1e8d11449443646eb20616525dc3e8/?ulp=' . $redirectUrl);//admitad
        //header('Location: ' . 'http://epnredirect.ru/redirect/cpa/o/8a20f237c0ba70728802f3ed17f7c5dc?to=' . $redirectUrl);//krutye-veshi link EPN - d-x.su
        //header('Location: ' . 'http://epnredirect.ru/redirect/cpa/o/7c5d917e448a69c032c6222eef78c780?to=https://ru.aliexpress.com/item/xxx/'. $aliProductId .'.html');//desh-mag
        die();
    }
    elseif ($redirect)//без if бесконечный редирект
    {
        header('Location: /');
        die();
    }
}

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
(require_once __DIR__.'/../bootstrap/app.php')
    ->handleRequest(Request::capture());
