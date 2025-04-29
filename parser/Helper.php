<?php

class Helper
{

    public static function request($url, $postParams = [], $headers=[])
    {

        $ch = curl_init();
        if ($headers) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/118.0.0.0 Safari/537.36 OPR/104.0.0.0 (Edition Yx 05)');//Юзер агент
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);//Автоматом идём по редиректам
        curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_COOKIESESSION, TRUE);

        if ($postParams)
        {
            $postString = http_build_query($postParams);
            curl_setopt ($ch, CURLOPT_POSTFIELDS, $postString);
        }
        $content = curl_exec($ch);
        curl_close($ch);

        return $content;
    }

    public static function getAeContent($url, $postParams = [], $headers=[])
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

        /*
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($httpCode == 404)
        {
            $content = null;
        }*/

        curl_close($ch);

        return $content;
    }

    /**
     * @throws Exception
     */
    public static function parseContent(string $content): array
    {
        if (empty($content)) {
            throw new Exception('No content received');
        }
        if (strpos($content, '/punish?')) {
            throw new Exception('Captcha');
        }

        if (strpos($content, '404 | AliExpress') || strpos($content, 'Такой страницы нет')) {
            throw new Exception(ParserError::NotFound->value);
        }

        if (!strpos($content, 'SnowProductGallery_SnowProductGallery__container')) {
            throw new Exception(ParserError::WrongPage->value);//не страница с детальным описаниме товара
        }

        if (strpos($content, 'Товар уже разобрали</h3>')) {
            throw new Exception(ParserError::OutOfStock->value);
        }

        $matches = [];
        preg_match('/<script id="__AER_DATA__" type="application\/json">(.*?)<\/script><script src="https:\/\/st\.aestatic\.net\/mixer\/ssr\/1\/aer-assets\/system\.js">/isU', $content, $matches);
        $jsonText = $matches[1] ?? null;

        if (!$jsonText) {
            throw new Exception('Не удалось выделить код json из документа');
        }

        $json = json_decode($jsonText, JSON_OBJECT_AS_ARRAY);

        if (!$json) {
            throw new Exception('Невалидный json в документе');
        }

        $basic = self::findBasic($json);

        if (!$basic) {
            throw new Exception('Не удалось найти basic');
        }

        return static::getBasicData($basic);
    }

    public static function parseExtraContent(string $jsonTxt): array
    {
        $json = json_decode($jsonTxt, JSON_OBJECT_AS_ARRAY);

        if (!$json) {
            throw new Exception('Невалидный json в extra документе');
        }

        $data = [];

        $data['description'] = static::findDescription($json);


//        $properties = [];
//        $i=0;
//        $propBasic = static::findProperties($json);
//        if ($charBasic) {
//            while (isset($charBasic[$i])) {
//                $characteristics[] = $charBasic[$i];
//                $i++;
//            }
//        }

        $reviewBasic = $json["widgets"]["2"]["state"]["data"]["reviews"] ?? null;
        if ($reviewBasic) {
            $data['reviews'] = json_encode($reviewBasic, JSON_UNESCAPED_UNICODE);
        }

//        if ($properties) {
//            $data['properties'] = '<ul class="product-property-list">';
//            foreach ($properties as $c) {
//                if (isset($c['title']) && isset($c['value']))
//                    $data['properties'] .= '<li class="property-item"><span class="propery-title">'.$c['title'].'</span><span class="propery-des">'.$c['value'].'</span></li>';
//            }
//            $data['properties'] .= '</ul>';
//        }
        return $data;
    }

    private static function buildArray(array $data, array $tails): array|string
    {
        $item = $data;
        foreach ($tails as $key) {
            if (isset($item[$key])) {
                $item = $item[$key];
            } else {
                $item = [];
            }
        }

        return $item;
    }

    private static function findDescription($json): array|string|null
    {
        $templates = [
            ["widgets", "0","state","data","html"],
            ["widgets", "10","state","data","html"],
            ["widgets", "11","state","data","html"],
        ];

        foreach ($templates as $t) {
            $tArr = static::buildArray($json, $t);
            if ($tArr) {
                return $tArr;
            }
        }

        return null;
    }

//    private static function findProperties($json)
//    {
//        $templates = [
//            ["widgets", "0","state","data","char"],
//            ["widgets", "0","state","data","char"],
//            ["widgets", "1","state","data","char"],
//        ];
//
//        foreach ($templates as $t) {
//            $tArr = static::buildArray($json, $t);
//            if ($tArr) {
//                return $tArr;
//            }
//        }
//
//        return null;
//    }

    private static function findBasic($json): ?array
    {
        $basics = [
            ["widgets","1","children","0","children","0","children","0","children","0","children","0","children","0","children","0","children","0","children","0"],
            ["widgets","1","children","0","children","0","children","0","children","0","children","0","children","0","children","0","children","0","children","0","children","0"],
            ["widgets","1","children","0","children","0","children","0","children","0","children","0","children","0","children","0","children","0","children","0","children","0","children","0"],
            ["widgets","1","children","0","children","0","children","0","children","0","children","0","children","0","children","0","children","0","children","0","children","0","children","0","children","0"],
            ["widgets","0","children","0","children","0","children","0","children","0","children","0","children","0","children","0","children","0","children","0","children","0","children","0","children","0"],
            ["widgets","0","children","0","children","0","children","0","children","0","children","0","children","0","children","0","children","0","children","0","children","0","children","0","children","0","children","1"],
            ["widgets","0","children","0","children","0","children","0","children","0","children","0","children","0","children","0","children","0","children","0","children","0","children","0","children","0","children","1","children","0"],
        ];

        foreach ($basics as $b) {
            $bArr = static::buildArray($json, $b);
            if (isset($bArr["props"]["id"])) {
                return $bArr;
            }
        }

        return null;
    }

    private static function getBasicData(array $basic): array
    {
        $lowPrice = $basic["props"]["price"]["minActivityAmount"]["value"];
        $highPrice = $basic["props"]["price"]["maxActivityAmount"]["value"];
        if (empty($lowPrice))
        {
            $lowPrice = $basic["props"]["price"]["minAmount"]["value"];
            $highPrice = $basic["props"]["price"]["maxAmount"]["value"];
        }
        $price = $lowPrice;
        /////////////////////
        $rating =   $basic["props"]["rating"]["middle"]  ;
        $rating = floatval($rating)*10;
        /////////////////////
        $imgList = $basic["props"]["gallery"];
        $img = [];
        $video = [];
        foreach ($imgList as $image)
        {
            $img[] = $image['imageUrl'];
            if ($image['videoUrl'] && !is_numeric($image['videoUrl']))
                $video[] = $image['videoUrl'];
        }
        //////////////////////////
//        $propsList = $basic["children"]["8"]["children"]["1"]["children"]["0"]["children"]["3"]["children"]["0"]["children"]["1"]["props"]["groups"]["0"]["properties"];
//        $props = '';
//        if (is_array($propsList) && count($propsList))
//        {
//            $props = '<ul class="product-property-list">';
//            foreach ($propsList as $p)
//            {
//                $props .= '<li class="property-item">';
//                $props .= '<span class="propery-title">' . (isset($p['title']) ? $p['title'] : $p['name'])  . ':</span>';
//                $props .= '<span class="propery-des">' . $p['value'] . '</span>';
//                $props .= '</li>';
//            }
//            $props .= '</ul>';
//        }
        ////////////////////////////
//        $reviewList = $basic["props"]["reviews"];
//        $reviews = [];
//        $i = 0;
//        while (isset($reviewList[$i])) {
//            $i++;
//            $reviews[] = $reviewList[$i];
//        }
//        $reviews = $reviews ? json_encode($reviews, JSON_UNESCAPED_UNICODE) : '';

        ///////////////////////////////////
        $breadCrumbs = $basic["children"]["7"]["children"]["0"]["props"]["breadcrumbs"];

        $brcr = array();
        foreach ($breadCrumbs as $bc)
        {
            $href = $bc['url'];
            $hrefParts = explode('/', $href);
            $hru = array_pop($hrefParts);
            $hru = str_replace('.html', '', $hru);
            $idCat = array_pop($hrefParts);
            if (strpos($href,  '/category/'))
                $brcr[] = array('id_ae' => $idCat, 'hru' => $hru,  'title' => $bc['name']);
        }



        return
            [
                'brcr' => $brcr,
                'data' =>
                [
                    'id_ae' => $basic["props"]["id"],
                    'title_ae' => $basic["props"]["name"],
                    'category_id' => $brcr[count($brcr) -1]['id_ae'],
                    'category_0' => $brcr[0]['id_ae'] ?? null,
                    'category_1' => $brcr[1]['id_ae'] ?? null,
                    'category_2' => $brcr[2]['id_ae'] ?? null,
                    'category_3' => $brcr[3]['id_ae'] ?? null,
                    'price' => $price,
                    'priceLow' => $lowPrice,
                    'priceHigh' => $highPrice,
                    'rating' => $rating,
                    'photo' => $img,
                    'video' => $video,
                    //'properties' => $props,
                    //'reviews' => $reviews
                ]
        ];
    }

    public static function validateErrors(array $aliData): array
    {
        $errors = [];

        if (!$aliData['id_ae']) {
            $errors[] = 'empty id_ae';
        }

        if (!count($aliData['photo'])) {
            $errors[] = 'no photos';
        }

        if (!$aliData['category_id']) {
            $errors[] = 'no category_id';
         }

         if (!$aliData['description'] && !$aliData['properties'] && !$aliData['reviews']) {
              $errors[] = 'empty description and properties and reviews';
         }

         return $errors;
    }
}
