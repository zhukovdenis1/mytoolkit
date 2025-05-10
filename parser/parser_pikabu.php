<?php

require_once 'Helper.php';
require_once 'ParseError.php';
$config = include 'config.php';

$captchaCounter = 0;

if (!$config['debug']) sleep(mt_rand(0,25));

$json = Helper::request($config['url_shop'] . $config['coupons']['get_uri']);

$data = json_decode($json, true);

$uri = $data['data']['info']['url'] ?? null;

$couponId = $data['data']['id'] ?? 0;

$json = [];

$foundUrl = '';

try {
    if (empty($data['data'])) {
        throw new Exception('No new data received');
    }

    if (!$uri) {
        throw new Exception('No pikabu uri');
    }

    $pikabuUrl = $config['coupons']['pikabu_url'] . $uri;

    $content = Helper::getAeContent($pikabuUrl);

    if (empty($content)) {
        throw new Exception('No content received');
    }
    if (strpos($content, '/punish?')) {
        throw new Exception('Captcha');
    }

    if (strpos($content, '404 | AliExpress') || strpos($content, 'Такой страницы нет')) {
        throw new Exception(ParserError::NotFound->value);
    }

    // Регулярное выражение для извлечения значения request.URL
    if (preg_match('/"request\.URL":"([^"]+)"/', $content, $matches)) {
        $url = $matches[1];
        // Декодирование Unicode-символов (например, \u0026 -> &)
        $url = json_decode('"' . str_replace('"', '\"', $url) . '"');
        $url = trim($url);
        $url = strtok($url, '?');
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new Exception('URL not valid');
        }
        $newUrl = $url;
    } else {
        throw new Exception('URL not found');
    }
    $json = Helper::request($config['url_shop'] . $config['coupons']['set_uri'], [
        'url' => $newUrl,
        'coupon_id' => $couponId
    ]);
    //echo $json.PHP_EOL;
    echo date('H:i:s ') . 'ok: coupon_id:' . $couponId . ' url=' . $newUrl;
} catch (Exception $e) {
    $message = $e->getMessage();
    $errorCode = 0;
    if (is_numeric($message)) {
        $errorCode = $message;
        $message = ParserError::getMessageByCode($errorCode);
    }
    if ($message == 'Captcha') {
        $captchaCounter++;
        sleep(60*$captchaCounter);
    } else {
        $captchaCounter--;
        $json = Helper::request($config['url_shop'] . $config['coupons']['set_uri'], [
            'url' => '',
            'coupon_id' => $couponId
        ]);
    }

    echo date('H:i:s ') . 'er: ' . $message . ' coupon_id:' . $couponId;
}





