<?php

class Helper
{
    public static $version = 0;
    private static $connect = 0;
    private static array $uuids = [];

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

    public static function getAeContent($url, $postParams = [], $headers=[]/*, $referer = null*/)
    {
        $referer = null;
        $connects = array(
            0 => [
                'i' => 0,
                'useragent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/118.0.0.0 Safari/537.36 OPR/104.0.0.0 (Edition Yx 05)',
                'referer' => $referer ?? 'https://yandex.ru/',
            ],
            1 => [
                'i' => 1,
                'useragent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:91.0) Gecko/20100101 Firefox/91.0 SeaMonkey/2.53.17.1',
                'referer' => $referer ?? 'https://google.com/',
            ],
            2 => [
                'i' => 2,
                'useragent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/119.0.0.0 Safari/537.36',
                'referer' => $referer ?? 'https://mail.ru/',
            ],
            3 => [
                'i' => 3,
                'useragent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/119.0.0.0 Safari/537.36 Edg/119.0.0.0',
                'referer' => $referer ?? 'https://mail.ru/',
            ],
        );

        $connect = $connects[static::$connect];

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
        curl_setopt($ch, CURLOPT_COOKIEFILE,'db/cookie' . $connect['i'].'.txt' );//запись
        curl_setopt($ch, CURLOPT_COOKIEJAR, 'db/cookie' . $connect['i'].'.txt' );//чтение
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
    public static function getJsonFromContent(string $content): array
    {
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

        return $json;
    }

    /**
     * Парсинг детальной страницы товара
     * @param string $content
     * @return array
     * @throws Exception
     */
    public static function parseContent(string $content): array
    {
        static::$version = 0;
        static::$connect = mt_rand(0,3);
        if (empty($content)) {
            throw new Exception('No content received');
        }
        if (strpos($content, '/punish?')) {
            throw new Exception('Captcha');
        }

        if (strpos($content, '404 | AliExpress') || strpos($content, 'Такой страницы нет')) {
            throw new Exception(ParserError::NotFound->value);
        }

        if (strpos($content, 'HazeProductGridItem_HazeProductGridItem__item__1xcur')) {
            static::$version = 2;//example: https://aliexpress.ru/item/32798240122.html?gatewayAdapt=glo2rus&sku_id=64043994862
        } elseif (strpos($content, 'SnowProductGallery_SnowProductGallery__container')) {
            static::$version = 1;
        } else {
            throw new Exception(ParserError::WrongPage->value);//не страница с детальным описаниме товара
        }

        if (strpos($content, 'Товар уже разобрали</h3>')) {
            throw new Exception(ParserError::OutOfStock->value);
        }

        $json = self::getJsonFromContent($content);

        $basic = self::findBasic($json);

        if (!$basic) {
            throw new Exception('Не удалось найти basic');
        }
        static::extractAndSetUuids($json);
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


        $reviewBasic = null;
        $i=0;
        while (!$reviewBasic && $i<20) {
            $reviewBasic = $json["widgets"][$i]["state"]["data"]["reviews"] ?? null;
            $i++;
        }

        if ($reviewBasic) {
            $data['reviews'] = json_encode($reviewBasic, JSON_UNESCAPED_UNICODE);
        }

        $propsList = null;
        $i=0;
        while (!$propsList && $i<20) {
            $propsList = $json["widgets"][$i]["state"]["data"]["groups"]["0"]["properties"] ?? null;
            $i++;
        }

        $props = '';
        if (is_array($propsList) && count($propsList))
        {
            $props = '<ul class="product-property-list">';
            foreach ($propsList as $p)
            {
                $props .= '<li class="property-item">';
                $props .= '<span class="propery-title">' . (isset($p['title']) ? $p['title'] : $p['name'])  . ':</span>';
                $props .= '<span class="propery-des">' . $p['value'] . '</span>';
                $props .= '</li>';
            }
            $props .= '</ul>';
        }

        $data['characteristics'] = $props;

        return $data;
    }

    public static function parseExtra2Content(string $jsonTxt): array
    {

        $json = json_decode($jsonTxt, JSON_OBJECT_AS_ARRAY);

        if (!$json) {
            throw new Exception('Невалидный json в extra2 документе');
        }

        $data = [];

        $data['title_ae'] = $json['data']['name'];

        $imgList = $json['data']["gallery"];
        $img = [];
        $video = [];
        foreach ($imgList as $image)
        {
            $img[] = $image['imageUrl'];
            if ($image['videoUrl'] && !is_numeric($image['videoUrl']))
                $video[] = $image['videoUrl'];
        }

        $data['photo'] = $img;

        //$data['price'] = $json["data"]["skuInfo"]["priceList"]["0"]["activityAmount"]["value"];
        $priceLow = $json["data"]["price"]["minAmount"]["value"] ?? null;
        $data['price_from'] = (int) $priceLow;
        $priceHigh = $json["data"]["price"]["maxAmount"]["value"] ?? null;
        $data['price_to'] = (int) $priceHigh;
        $data['price'] = $data['price_from'] ?: $data['price_to'];

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
        $templates = [];
        for ($i=0; $i<20; $i++) {
            $templates[] = ["widgets", strval($i),"state","data","html"];
        }

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
        $lowPrice = $basic["props"]["price"]["minActivityAmount"]["value"] ?? null;
        $highPrice = $basic["props"]["price"]["maxActivityAmount"]["value"] ?? null;
        if (empty($lowPrice))
        {
            $lowPrice = $basic["props"]["price"]["minAmount"]["value"] ?? null;
            $highPrice = $basic["props"]["price"]["maxAmount"]["value"] ?? null;
        }
        $price = $lowPrice;
        /////////////////////
        $rating =   $basic["props"]["rating"]["middle"] ?? null;
        $rating = $rating ?: $basic["children"]["8"]["children"]["1"]["children"]["0"]["children"]["2"]["children"]["0"]["children"]["2"]["children"]["0"]["children"]["0"]["props"]["analyticEvents"]["viewWidgetReview"]["trackingInfo"]["overallRating"] ?? '';
        $rating = floatval(str_replace(',', '.', $rating))*10;
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
        ////////////////////////
        $propsList = $basic["children"]["8"]["children"]["1"]["children"]["0"]["children"]["3"]["children"]["0"]["children"]["1"]["props"]["groups"]["0"]["properties"] ?? null;
        $props = '';
        if (is_array($propsList) && count($propsList))
        {
            $props = '<ul class="product-property-list">';
            foreach ($propsList as $p)
            {
                $props .= '<li class="property-item">';
                $props .= '<span class="propery-title">' . (isset($p['title']) ? $p['title'] : $p['name'])  . ':</span>';
                $props .= '<span class="propery-des">' . $p['value'] . '</span>';
                $props .= '</li>';
            }
            $props .= '</ul>';
        }
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
                    'price_from' => $lowPrice,
                    'price_to' => $highPrice,
                    'rating' => $rating,
                    'photo' => $img,
                    'video' => $video,
                    'characteristics' => $props,
                    //'reviews' => $reviews
                ]
        ];
    }

    public static function validateErrors(array $aliData, array $queueData): array
    {
        $errors = [];
        $canBeEmptyString = $queueData['empty'] ?? '';
        $canBeEmpty = array_merge(explode(',', $canBeEmptyString), ['price_from', 'price_to', 'video']);

        foreach ($aliData as $k => $v) {
            if (empty($v) && !in_array($k, $canBeEmpty)) {
                $errors[] = 'empty ' . $k;
            }
        }

        return $errors;

        /*$errors = [];

        if (!$aliData['id_ae']) {
            $errors[] = 'empty id_ae';
        }

        if (!$aliData['rating']) {
            $errors[] = 'no rating';
        }

        if (!$aliData['price']) {
            $errors[] = 'empty price';
        }

        if (!count($aliData['photo'])) {
            $errors[] = 'no photos';
        }

        if (!$aliData['category_id']) {
            $errors[] = 'no category_id';
         }

        if (!$aliData['characteristics']) {
            $errors[] = 'empty properties';
        }

         if (!$aliData['description']) {
              $errors[] = 'empty description';
         }

        if (!$aliData['reviews']) {
            $errors[] = 'empty reviews';
        }

         return $errors;*/
    }

    private static function extractAndSetUuids(array $array): array {
        $uuids = [];

        foreach ($array as $key => $value) {
            if ($key === 'uuid') {
                $uuids[] = $value;
            }

            if (is_array($value)) {
                $uuids = array_merge($uuids, static::extractAndSetUuids($value));
            }
        }
        static::$uuids = $uuids;
        return $uuids;
    }

    public static function formExtraContentUrl(): string
    {
        $url = 'https://aliexpress.ru/widget?';

        $uuids = [
            '27a9f04d-c23c-4aa7-9446-572f753a5305',
            'ae72b0f5-8ee3-4967-a5b5-8c84292fc0de',//char
            'd8e734d3-0347-4a7e-a8eb-fe3826745659',
            'd30d4e7e-1683-4300-b724-31fc418fdac7',//reviews
            '008d7ddf-ddb8-44ee-8f0b-49b779857027',
            'fa76ca80-52f3-4bb3-95db-f46d96760bb5',
            'c3eea9e2-c6a5-4239-9656-8ef38da58334',
            '5e035d48-df37-4901-9711-977dca5b6da8',
            '43946a0a-40f8-48e9-a5bd-5a53598e37db',
            '55cedca7-d9f4-4a8e-96c4-b94df7f5cc66',
            'ef9105ef-a550-433f-bdf2-0637bd47c32f',
            'fed85114-3104-453b-bc5d-ca001922ebde',
            'e1459484-97b0-4e41-a0be-e06fb8a0ff01',//description
            '3398848f-8cbc-4d62-81c8-d915468dde12',
            'a5b4609f-cdac-4ad5-9bec-9cd518a056e6',
            'f38e308c-ef6f-467b-b12c-e8e4dce2b728',
            '8dec9aaf-2124-48d2-bf39-d390832c4152',
            'ac237c57-23d7-42d7-8b08-3b78407e0045'
        ];

        foreach (static::$uuids as $uuid) {
            if (in_array($uuid, $uuids)) {
                $url .= 'uuid=' . $uuid . '&';
            }
        }
        $url .= '_bx-v=2.5.28';
        return $url;
    }

    public static function merge(array $array1, array $array2): array
    {
        foreach ($array2 as $key => $value) {
            if (empty($array1[$key])) {
                $array1[$key] = $value;
            }
        }

        return $array1;
    }
}
