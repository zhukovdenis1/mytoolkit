<?php

require_once 'Helper.php';
require_once 'ParseError.php';

$config = include 'config.php';
$output = PHP_EOL;
$dbFile = dirname(__FILE__).'/db/parser.json';
$logFile = dirname(__FILE__).'/../storage/logs/parser-'.date('Y-m').'.log';

$db = file_get_contents($dbFile);
$dbJson = json_decode($db, true);

$dbCaptchaCounter = $dbJson['captchaCount'] ?? 0;
$dbLastRequestDateTime = $dbJson['lastRequest'] ?? 0;
$delay = 30*($dbCaptchaCounter+1);

if ((strtotime($dbLastRequestDateTime) + $delay) > time()) {
    die('*');
}

//if (!$config['debug']) sleep(mt_rand(0,25));

$parseUrl = '';
$qData = ['id' => 1];//for debug case
$message = '';
try {
    if ($config['debug'] && $config['debug_source'] == 'file') {
        $parseUrl = 'file:' . $config['debug_file'];
        $content = file_get_contents($config['debug_file']);
    } elseif ($config['debug'] && $config['debug_source'] == 'url') {
        $parseUrl = $config['debug_parse_url'];
        $content = Helper::getAeContent($parseUrl);
        file_put_contents($config['debug_file'], $content);
    } else {
        $json = Helper::request($config['url'] . $config['get_uri']);

        $qData = json_decode($json, true);
        if (empty($qData['data'])) {
            throw new Exception('Empty parse queue');
        }
        $qData = $qData['data'];

        $queueItemId = $qData['id'];
        if ($qData['source'] == 'epn_hot') {
            $parseUrl = $qData['info']['attributes']['directUrl'];
        } elseif ($qData['source'] = 'epn_top') {
            $parseUrl = 'https://aliexpress.ru/item/'.$qData['id_ae'].'.html';
        } else {
            throw new Exception('Unknown source');
        }
        $content = Helper::getAeContent($parseUrl);
    }

    $parsedData = Helper::parseContent($content);
    $baseData = $parsedData['data'];
    $brcr = $parsedData['brcr'];

    if (!$baseData['id_ae']) {
        throw new Exception("Can't get id_ae in Helper::parseContent");
    }
    if ($config['debug'] && $config['debug_source'] == 'file') {
        $extraContent = file_get_contents($config['debug_extra_file']);
    } else {
        $extraUrl = Helper::formExtraContentUrl();
        $extraContent = Helper::getAeContent(
            //Helper::$version == 2 ? $config['url_extra_2'] : $config['url_extra'],
            $extraUrl,
            [],
            ['Aer-Url: https://aliexpress.ru/item/' . $baseData['id_ae'] . '.html']
        );

        if (($config['debug'] && $config['debug_source'] == 'url')) {
            file_put_contents($config['debug_extra_file'], $extraContent);
        }
        file_put_contents($config['debug_extra_file'], $extraContent);
    }

    $extraData = Helper::parseExtraContent($extraContent);

    $aliData = Helper::merge($baseData, $extraData);

    $extra2Content = '';
    if (Helper::$version == 2) {
        if ($config['debug'] && $config['debug_source'] == 'file') {
            $extra2Content = file_get_contents($config['debug_extra2_file']);
        } else {
            $extra2Content = Helper::getAeContent(
                'https://aliexpress.ru/aer-jsonapi/v1/bx/pdp/web/productData?productId='.$baseData['id_ae'],
                [],
                []
            );

            if ($config['debug'] && $config['debug_source'] == 'url') {
                file_put_contents($config['debug_extra2_file'], $extra2Content);
            }
            file_put_contents($config['debug_extra2_file'], $extra2Content);
        }

        $extra2Data = Helper::parseExtra2Content($extra2Content);

        $aliData = Helper::merge($aliData, $extra2Data);
    }

    $validateErrors = Helper::validateErrors($aliData, $qData);

    if ($validateErrors) {
        $output.= implode(PHP_EOL, $validateErrors) . PHP_EOL;
        throw new Exception(ParserError::ValidationError->value);
    }

    if (empty($qData['id'])) {
        throw new Exception(ParserError::EmptyIdQueue->value);
    }

    if ($config['debug']) {
        var_dump(['id_queue' => $qData['id'], 'data' => $aliData, 'brcr' => $brcr, 'version' => Helper::$version]);
    } else {
        $response = Helper::request($config['url'] . $config['set_uri'],
            ['id_queue' => $qData['id'], 'data' => $aliData, 'brcr' => $brcr, 'version' => Helper::$version]
        );
//        var_dump($data['id']);
        file_put_contents('response.html', $response);

        $responseData = json_decode($response, true);

        $output .= date('H:i:s ') . 'v'. Helper::$version . ' ok: ' . $config['url_shop'] . '/p-' . $responseData['data']['product']['id'] . '/ ; id_queue='.$qData['id'] . ' ; id_ae='. $qData['id_ae'];
    }
} catch (Exception $e) {
    $message = $e->getMessage();
    $errorCode = 0;

    if (is_numeric($message)) {
        $errorCode = $message;
        $message = ParserError::getMessageByCode($errorCode);
    }

    if (!$config['debug'] && $errorCode) {

        $json = Helper::request($config['url'] . $config['set_uri'], [
            'id_queue' => $qData['id'],
            'error_code' => $errorCode,
            'version' => Helper::$version,
        ]);
    }

    $output .= date('H:i:s ') . 'v'. Helper::$version. ' er: ' . $message . ' url:' . $parseUrl .  ' ; id_queue='.$qData['id'];
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
