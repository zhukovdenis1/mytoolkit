<?php

declare(strict_types=1);

require_once 'Helper.php';

$config = include dirname(__FILE__).'/config.php';
$output = PHP_EOL;
$dbFile = dirname(__FILE__).'/db/parser.json';
$logFile = dirname(__FILE__).'/../storage/logs/parser-reviews-'.date('Y-m').'.log';

$db = file_get_contents($dbFile);
$dbJson = json_decode($db, true);

$dbCaptchaCounter = $dbJson['captchaCount'] ?? 0;
$dbLastRequestDateTime = $dbJson['lastRequest'] ?? 0;
$delay = 30*($dbCaptchaCounter+1);

if ((strtotime($dbLastRequestDateTime) + $delay) > time()) {
    die('*');
}

try {
    $json = Helper::request($config['url_shop'] . $config['reviews']['get_uri']);
    $data = json_decode($json, true);

    $idAe = $data['data']['id_ae'] ?? null;
    $productId = $data['data']['id'] ?? null;

    if (!$idAe) {
        throw new Exception('Product not found');
    }

    if (empty($data['extra_data']['reviews']['tags'])) {
        $json = Helper::getAeContent('https://aliexpress.ru/aer-jsonapi/review/v1/desktop/product-ml-tags', '{"productKey": {"id": "' . $idAe . '", "sourceId": 0}}');

        $data = json_decode($json, true);

        $tags = empty($data['data']['tags']) ? null : json_encode($data['data']['tags'], JSON_UNESCAPED_UNICODE) ;
        $response = Helper::request($config['shop_url'] . $config['reviews']['set_tags_uri'],
            ['product_id' => $productId, 'tags' => $tags]
        );

        $output .= date('H:i:s ') . ' ok: ' . ' ; id_ae='. $idAe . '; result(tags)' . $response;

    } else {
        $pageNum = empty($data['extra_data']['reviews']['parse']['currentPage']) ? 1 : (int) $data['extra_data']['reviews']['currentParsePage'] + 1;
        $pageSize = empty($data['extra_data']['reviews']['parse']['pageSize']) ? 10 : (int) $data['extra_data']['reviews']['parse']['pageSize'];

        $json = Helper::getAeContent('https://aliexpress.ru/aer-jsonapi/review/v5/desktop/product-reviews?_bx-v=2.5.31', '{"productKey":{"id":"' . $idAe . '","sourceId":0},"pagination":{"pageNum": ' . $pageNum . ',"pageSize":' . $pageSize . '},"sort":1,"filters":[]}');

        $data = json_decode($json, true);

        if (!isset($data['data']['reviews'])) {
            throw new Exception('Reviews request error');
        }

        $result = [];

        $i = 0;
        foreach ($data['data']['reviews'] as $review) {
            $images = [];
            if (!empty($review['root']['images'])) {
                foreach ($review['root']['images'] as $image) {
                    $images[] = [
                        'id' => $image['id'],
                        'url' => $image['url'],
                    ];
                }
            }

            $i++;
            $result[] = [
                'id_ae' => $review['root']['id'],
                'product_id' => $productId,
                'product_id_ae' => $idAe,
                'date' => formatReviewDate($review['root']['date'] ?? null),
                'grade' => $review['root']['grade'] ?? null,
                'text' => $review['root']['text'] ?? '',
                'reviewer' => empty($review['reviewer']) ? null : json_encode([
                    'name' => $review['reviewer']['name'],
                    'avatar' => $review['reviewer']['avatar'],
                    'countryFlag' => $review['reviewer']['countryFlag'],
                ], JSON_UNESCAPED_UNICODE),
                'images' => $images ? json_encode($images, JSON_UNESCAPED_UNICODE) : null,
                'likesAmount' => $review['interaction']['likesAmount'] ?? 0,
                'sort' => ($pageNum-1)*$pageSize + $i,
                'additional' => empty($review['additional']) ? null : json_encode([
                    'id' => $review['additional']['id'],
                    'date' => formatReviewDate($review['additional']['date'] ?? null),
                    'grade' => $review['additional']['grade'],
                    'text' => $review['additional']['text'],
                ], JSON_UNESCAPED_UNICODE),
                'raw' => json_encode($review, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT),
            ];
        }

        $response = Helper::request($config['shop_url'] . $config['reviews']['set_uri'],
            ['product_id' => $productId, 'reviews' => $result, 'page' => $pageNum, 'limit' => $pageSize]
        );

        $output .= date('H:i:s ') . ' ok: ' . ' ; id_ae='. $idAe . '; result' . $response;
    }


} catch (Exception $e) {
    $message = $e->getMessage();

    $output .= date('H:i:s ') . ' er: ' . $message ;
}


echo $output;


function formatReviewDate(?string $dateString): ?string
{
    if (!$dateString) {
        return null;
    }

    $dateString = str_replace('Дополнен ', '', $dateString);

// Создаем массив соответствий русских названий месяцев английским
    $months = [
        'января' => 'January',
        'февраля' => 'February',
        'марта' => 'March',
        'апреля' => 'April',
        'мая' => 'May',
        'июня' => 'June',
        'июля' => 'July',
        'августа' => 'August',
        'сентября' => 'September',
        'октября' => 'October',
        'ноября' => 'November',
        'декабря' => 'December'
    ];

   // Заменяем русское название месяца на английское
    foreach ($months as $ru => $en) {
        $dateString = str_replace($ru, $en, $dateString);
    }

    // Преобразуем строку в дату и форматируем
    $date = DateTime::createFromFormat('j F Y', $dateString);
    $formattedDate = null;
    if ($date) {
        $formattedDate = $date->format('Y-m-d');
    }

    return $formattedDate;
}

/*
var_dump($json);die;

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

$connect = $connects[1];

$url = 'https://aliexpress.ru/aer-jsonapi/review/v5/desktop/product-reviews?_bx-v=2.5.31';

$headers = [
//    'authority: aliexpress.ru',
//    'method: POST',
//    'path: /aer-jsonapi/review/v5/desktop/product-reviews?_bx-v=2.5.31',
//    'scheme: https',
//    'accept-encoding: gzip, deflate, br, zstd',
//    'accept-language: ru,en-US;q=0.9,en;q=0.8,bg;q=0.7,zh-TW;q=0.6,zh;q=0.5',
//    'bx-ua: 231!+t+7c+mUKSk+jIc+A+7hUHRjUGiPFkEQs19Y0XW9bJP1HrmQcgohokLSBN5BjtdHzGmZA1UgdL35lpou6O9psBOpREvQpavcdSPiF94wEaN/ST4n8Db0WXYeUAntru0eoC9HfmPHCdLrLz5Ny1cCpsEyPlD3RsQ3TFwRjLRZDxpNCT7n8OSDk3QTFOgbPOqtuUtgWw26gfCkm9i76oq1N6ZoiVAnsAkilH1GWKHB63gRjkeEC0YsCRe294scY7dRZ53Lm8NVx0KrdycsiPkr+hMep4Dqk+I9xGdF19OUxglCok+++4mWYi++6bJoobb/DxADj+8GiSRrAQzdstocfIhc4FQjiU17oSvEajofTh+bCBhvkovtqWkjQWLqp3Cf60jvB2t8dVW6+T3YAbX03y+a6x5NtgZsrFksnJBp2L4JVQhmthxlQAF78hQMrR42qkRb7TB46S91092dW7M5EOVRmzSKv8evFRpkTVSZqU/SeE5XfaejWlbxP8Va2eCavaHIySLMtfL77RdWIbyU7/BdQm1BckEWArdq0qYgbh/8+EnxUAWrudXJkFooXLaFhyUaNcCzhzZbMbupNNCLHexMf6buxNFujKsyGipCZyUiRuE3YEiPdAXIrfBzfRzJ6FNecEwkMk4fnjQ2qfKO6yjukiJtcjxen83bKtLmpCtm/mQrCZ3LrOY0Ynnu7KsPaNK101CP4NmDgEUWm7DAvDEsoEtiNbJWExGvz8J4A66G7XqupfGeeX3oU1EjTO6W2xOSfcRUQt1GQ8Mw1G37yap55AgTyUR0tcei1ajR7g3d4uMMOnSTqGaJeO3ENpZSFhlRl4wGATXm1a3Kcvjsd+DkqGIYtY1b7wvdl27R/H9Czsm8ze/9cN4btZg/QqH5NKT7SZ/eaxYzutAlde0XRux8tKM9iBhHoQ/rptsVYWMc+YQ+dHp8YXrYcXQB39KbfEh5seEiWseHS6E9Iz627HJtWTCds7692ID3Dte2m9w0pSy1imhw1UDcxzDHcqnMtI2NRe3xE5MPaxWKBG7KKpDn4+bayy1FyrRel7g4tI0gAkeNkMb+hqMQEdg8uRUzRj3SwLkZGyOxR5hDaFr3FbbNXCBPSwmL78VlZ29Xc10JOMmA/V57cI8PnApkDYuILI4tVW5O6yg/dILuI4HYJWdX6ZW7pGJl4oZ5tp96ZX8DCzaNGTIl4nEVYK8pJCY4nE9NzflovWuNpBjpUsl/4dqmwhMBbJ3UsJp4t7wctj/+2RXCcKGlDR/hjYseFq6V/m6C2Cpf7MfKRFj6eYw1okr6AlVhza7MqAORNVm/9RsyFT/csc/JrDQOOAJkm0xPWnYCfhC68Ojay4LzGAZ4H06/ker8DDXzw/mF2C0iPLGrkTGljtnOw+1gcYaNadO94rTiXNSB0okYpGvJlLqSPhS5c11sO92OxcyVQ+15vsjgfd/q7y2o7bX2o8h1kVtrEJEcPptGVGTuIhJUYkXdRFMcOeTdTovd0TWSW4iY3BZnzpKk4MSmvmxjB5QROVZDDe2HXJXJ2RwoWZdDPI7EFt1kc6Y0L0Njt31F2kaet5f8+BW5aS8/TanK5RhL8Eu16wayYHpEieSSFR9I+enj8onvpcRAFCwi4u7tWiVBNNo5EvQk7ATM/bbSkwOX2fORhap4NXC3eG0gS72s6PjdfldEpj72hmu3uHpLmH5c9R83SGQNqu+VZ38f/Pf77Gr0KJUfSddD+Fo8/ybN26+5wfLr0gyr917+3+DtHsiQsMuqR+axwSHMv9BXDhC0owKFJ7rD4QTi+pESQzuV6IoyIX5jW9YmXmDpfE9hCNFAxD0CnqOah4EE49/pzxX9CcKDso67w/BKmm2oC5FF8U1kzRGiy2wd8dmSnumJpJGyr/bMIhKSHkMihsH8fQR4WxQF+aXrjOdqY5mljnziptCukA==',
//    'bx-umidtoken: T2gAXKdXYkp2VQI4VG05EaqrNBOH2SLnUhHdY7qLTlmDPTyCIsQQapbKg6q9GCJttc8=',
//    'bx-v: 2.5.31',
//    'cache-control: no-cache',
//    'content-type: application/json',
//    'cookie: ae_ru_pp_v=1.0.2; aer_rh=2063907076; aer_ec=kRz6IML9anHGQuglbj4nsQOXs6UuOvX5jhIouVNmBFkS4cfO0L/aSFaFxN4D5xqMJ3nzccLZIJtQ2BGmiOJsQaC8E6w80Kva/WCRg7B7rLw=; xman_t=3gDUBD506KhfHrl/eeV3I7+IikK5opoA24t7kNcVkP56EMsl9sYTgkCTtRjtVH1I; xman_us_f=x_locale=ru_RU&x_l=0&x_c_chg=1&acs_rt=b5d99fa09175434da56d3dc742426d5f; xman_f=BCUeOLQ10llO5dAA2igoGIR4k6x8/Ku2IOnHWAhRelJNN2Beg6FhSzG3ObZkf1Y9myOZzdWzTsRo5V9P0Faq4Obl4Zc+dUjAjMtt2mncKQKHehQTMwUukg==; acs_usuc_t=x_csrf=tfw5qw210i_h&acs_rt=b5d99fa09175434da56d3dc742426d5f; aep_usuc_f=b_locale=ru_RU&c_tp=RUB&region=RU&site=rus&province=917485680000000000&city=917485687390000000; aer_abid=bfa19d0faca8fb69..960996a563cd9eb2; cna=eyjoHqjf0hcCAVwn2j5LWiJ2; tmr_lvid=a02c53498d37512885d85ac4ce1b8736; tmr_lvidTS=1717647995826; adrcid=AzdeGKcM_oine_GMA9hpZVw; _ga=GA1.2.1210935169.1748320609; _ym_uid=1717647996928626981; _ym_d=1748320609; adrdel=1750394132224; autoRegion=lastGeoUpdate=1751263258&regionCode=917485680000000000&cityCode=917485687390000000&countryCode=RU&postalCode=426004&latitude=56.8489&longitude=53.2316; xlly_s=1; _gid=GA1.2.596030812.1751863768; _ym_isad=1; acs_3=%7B%22hash%22%3A%221aa3f9523ee6c2690cb34fc702d4143056487c0d%22%2C%22nst%22%3A1752038963322%2C%22sl%22%3A%7B%22224%22%3A1751952563322%2C%221228%22%3A1751952563322%7D%7D; _ym_visorc=b; domain_sid=7LbCBP8_3NSQNbLVDgZt1%3A1751952564796; a_r_t_info=%7B%22name%22%3A%22pdp%22%2C%22id%22%3A%22b9b65bc3-0f83-40e6-bf1d-df3d207daba6%22%2C%22chinaName%22%3A%22detail%22%2C%22referralUrl%22%3A%22https%3A%2F%2Faliexpress.ru%2Fitem%2F1005008081521104%2Freviews%22%7D; _gat_UA-164782000-1=1; a_t_info=%7B%22name%22%3A%22PRP%22%2C%22id%22%3A%22169048d6-a391-46e5-af95-6f61fb6a2598%22%2C%22chinaName%22%3A%22reviews%22%2C%22referralUrl%22%3A%22https%3A%2F%2Faliexpress.ru%2Fitem%2F1005008081521104%2Freviews%22%7D; tmr_detect=1%7C1751952633845; intl_common_forever=s7TzM594+M0Q5gVWAdkzKZ+MPdBXIUml6Bi8RP5VT+kNJv+qYBXOFg==; ali_apache_id=33.22.74.14.1751952634579.687131.0; JSESSIONID=2454942BEAB50D9E2FB2AF677549F952; tfstk=gezmDQg3koof5HX-2zgfYKzozJjRMqgs1RLtBVHN4Yk5GVEAGCPasJYvk5o9782YTonxumPzaXhZQduw748rsfcqQAuqbFV3nopxHPqWSWN_ksgOGqNj5VWdpwUgGSg_41_JArDy4fPy7hlV24z4JUmdpwQLgO0Tj4XLWRU3rYhZ7jow7b5oOXYZ7cl4a_cZ6K-wuR5lZflpbjlZ3bRrGXuZQRuNZ4kSsVl4QVlkFFk37zaPeCXBfOcOezhmmvPq3RF7zMiXcSDk7FzuqbkFeYYw7zc4Ny4TAejtLkMxvvyPyE0gaf2ovoXHoRVaOlouSK5YL8rgQbZ5IhD3bSEQy0Ow074inDzqqBYza4haQcq5Kehmkum3oo16USyKnkub6BXYZDqoAboysnuYvWUsYr7DdYnIs8mLm9Y0LgyX4H-dWFGP6z-6fmlSZv3_uPYrPXguH_fkAiiqNjBdZ_x6fmlSZvClZH9j0bGAp; isg=BIKCZb19XjHF6kzgxnfx5GeQ04jkU4ZthhwUesyamvWBHyOZqeK9fNkRzwNjT_4F',
//    'origin: https://aliexpress.ru',
//    'pragma: no-cache',
//    'priority: u=1, i',
//    'referer: https://aliexpress.ru/item/1005008081521104/reviews?sku_id=12000048377048263',
//    'sec-ch-ua: "Not)A;Brand";v="8", "Chromium";v="138", "Google Chrome";v="138"',
//    'sec-ch-ua-mobile: ?0',
//    'sec-ch-ua-platform: "Windows"',
//    'sec-fetch-dest: empty',
//    'sec-fetch-mode: cors',
//    'sec-fetch-site: same-origin',
//    'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36',
    //'x-requested-with: XMLHttpRequest'
];

$payload = [
    'productKey' => [
        'id' => '1005008081521104',
        'sourceId' => 0
    ],
    'pagination' => [
        'pageNum' => 2,
        'pageSize' => 10
    ],
    'sort' => 1,
    'filters' => []
];

$ch = curl_init();

curl_setopt_array($ch, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($payload),
    CURLOPT_HTTPHEADER => [],
    //CURLOPT_ENCODING => 'gzip, deflate, br, zstd',
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => false,
    CURLOPT_HEADER => true
]);

curl_setopt($ch, CURLOPT_COOKIEFILE,dirname(__FILE__).'/db/cookie' . $connect['i'].'.txt' );//запись
curl_setopt($ch, CURLOPT_COOKIEJAR, dirname(__FILE__).'/db/cookie' . $connect['i'].'.txt' );//чтение
curl_setopt($ch, CURLOPT_COOKIESESSION, TRUE);


$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);

$headers = substr($response, 0, $headerSize);
$body = substr($response, $headerSize);

curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "Response Body:\n";
print_r(json_decode($body, true));*/

