<?php

require_once 'Helper.php';
require_once 'ParseError.php';
$config = include 'config.php';
$output = PHP_EOL;
$dbFile = dirname(__FILE__).'/db/pikabu.json';
$logFile = dirname(__FILE__).'/../storage/logs/parser_pikabu-'.date('Y-m').'.log';

$db = file_get_contents($dbFile);
$dbJson = json_decode($db, true);

$dbCaptchaCounter = $dbJson['captchaCount'] ?? 0;
$dbLastRequestDateTime = $dbJson['lastRequest'] ?? 0;
$delay = 30*($dbCaptchaCounter+1);

if ((strtotime($dbLastRequestDateTime) + $delay) > time()) {
    die('*');
}

//if (!$config['debug']) sleep(mt_rand(0,25));

$json = Helper::request($config['url_shop'] . $config['coupons']['get_uri']);

$data = json_decode($json, true);
if (!empty($data['data']['info']) && is_string($data['data']['info'])) {
    $data['data']['info'] = json_decode($data['data']['info'], true);
}
$uri = $data['data']['info']['url'] ?? null;

$couponId = $data['data']['id'] ?? 0;

$json = [];

$foundUrl = '';

$message = '';

try {
    if (empty($data['data'])) {
        throw new Exception('No new data received');
    }

    if (!$uri) {
        throw new Exception('No pikabu uri');
    }

    $pikabuUrl = $config['coupons']['pikabu_url'] . $uri;
    $pikabuUrl = str_replace('\/', '/', $pikabuUrl);
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

    file_put_contents($config['coupons']['pikabu_debug_file'], $content);

    $url = '';

    // Регулярное выражение для извлечения значения request.URL
    if (preg_match('/"request\.URL":"([^"]+)"/', $content, $matches)) {
        $url = $matches[1];
        // Декодирование Unicode-символов (например, \u0026 -> &)
        $url = json_decode('"' . str_replace('"', '\"', $url) . '"');
    } elseif (preg_match('/<meta\s+content="([^"]+)"\s+property="og:url"\s*\/?>/i', $content, $matches)) {
        $url = $matches[1];
    }

    if (!$url) {
        throw new Exception('URL not found');
    }

    $url = trim($url);
    $url = strtok($url, '?');
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        throw new Exception('URL not valid');
    }

    $newUrl = $url;



    $json = Helper::request($config['url_shop'] . $config['coupons']['set_uri'], [
        'url' => $newUrl,
        'coupon_id' => $couponId
    ]);
    //$output .= $json.PHP_EOL;
    $output .= date('H:i:s ') . 'ok: coupon_id:' . $couponId . ' url=' . $newUrl . PHP_EOL;
} catch (Exception $e) {
    $message = $e->getMessage();
    $errorCode = 0;
    if (is_numeric($message)) {
        $errorCode = $message;
        $message = ParserError::getMessageByCode($errorCode);
    }
    if ($message != 'Captcha') {
        $json = Helper::request($config['url_shop'] . $config['coupons']['set_uri'], [
            'url' => '',
            'coupon_id' => $couponId
        ]);
    }

    $output .= date('H:i:s ') . 'er: ' . $message . ' coupon_id:' . $couponId . PHP_EOL;
}

if ($message == 'Captcha') {
    $dbCaptchaCounter++;
} else {
    $dbCaptchaCounter && $dbCaptchaCounter--;
}

$output .= ' cc=' . $dbCaptchaCounter;

file_put_contents($dbFile, json_encode([
    'captchaCount' => $dbCaptchaCounter,
    'lastRequest' => date('Y-m-d H:i:s')
]));

file_put_contents($logFile, $output, FILE_APPEND);

echo $output;





