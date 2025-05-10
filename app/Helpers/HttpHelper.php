<?php

declare(strict_types=1);

namespace App\Helpers;

class HttpHelper
{
    /*public function curlRequest(string $url, array $postParams = [], array $headers=[]): string
    {
        $connects = array(
            0 => [
                'i' => 0,
                'useragent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/118.0.0.0 Safari/537.36 OPR/104.0.0.0 (Edition Yx 05)',
                'referer' => 'https://yandex.ru/',
            ],
            1 => [
                'i' => 1,
                'useragent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:91.0) Gecko/20100101 Firefox/91.0 SeaMonkey/2.53.17.1',
                'referer' => 'https://google.com/',
            ],
            2 => [
                'i' => 2,
                'useragent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/119.0.0.0 Safari/537.36',
                'referer' => 'https://mail.ru/',
            ],
            3 => [
                'i' => 3,
                'useragent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/119.0.0.0 Safari/537.36 Edg/119.0.0.0',
                'referer' => 'https://mail.ru/',
            ],
        );

        $connect = $connects[mt_rand(0,3)];

        $ch = curl_init();
        if ($headers) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_USERAGENT, $connect['useragent']);//Юзер агент
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);//Автоматом идём по редиректам
        curl_setopt($ch, CURLOPT_REFERER, $connect['referer']);
        curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_COOKIEFILE,'cookie/cookie' . $connect['i'].'.txt' );//запись
        curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie/cookie' . $connect['i'].'.txt' );//чтение
        curl_setopt($ch, CURLOPT_COOKIESESSION, TRUE);

        if ($postParams)
        {
            $postString = http_build_query($postParams);
            curl_setopt ($ch, CURLOPT_POSTFIELDS, $postString);
        }

        $content = curl_exec($ch);


        curl_close($ch);

        return $content;
    }*/
}

