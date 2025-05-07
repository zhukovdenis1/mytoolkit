<?php

require_once 'Helper.php';
require_once 'ParseError.php';

$config = include 'config.php';

if (!$config['debug']) sleep(mt_rand(0,25));

$parseUrl = '';
$data = ['id' => 1];//for debug case
$captchaCounter = 0;

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
        $data = json_decode($json, true);
        $data = $data['data'];
        $queueItemId = $data['id'];
        if ($data['source'] == 'epn_hot') {
            $parseUrl = $data['info']['attributes']['directUrl'];
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
        $extraContent = Helper::getAeContent(
            $config['url_extra'],
            [],
            ['Aer-Url: https://aliexpress.ru/item/' . $baseData['id_ae'] . '.html']
        );

        if (($config['debug'] && $config['debug_source'] == 'url')) {
            file_put_contents($config['debug_extra_file'], $extraContent);
        }

    }

    $extraData = Helper::parseExtraContent($extraContent);

    $aliData = array_merge($baseData, $extraData);

    $validateErrors = Helper::validateErrors($aliData);

    if ($validateErrors) {
        echo implode(PHP_EOL, $validateErrors) . PHP_EOL;
        throw new Exception(ParserError::ValidationError->value);
    }

    if (empty($data['id'])) {
        throw new Exception(ParserError::EmptyIdQueue->value);
    }

    if ($config['debug']) {
        var_dump(['id_queue' => $data['id'], 'data' => $aliData, 'brcr' => $brcr]);
    } else {
        $response = Helper::request($config['url'] . $config['set_uri'],
            ['id_queue' => $data['id'], 'data' => $aliData, 'brcr' => $brcr]
        );
        file_put_contents('response.html', $response);

        $responseData = json_decode($response, true);

        echo date('H:i:s ') . 'ok: ' . $config['url_shop'] . '/p-' . $responseData['data']['id'] . '/';
    }
} catch (Exception $e) {
    $message = $e->getMessage();
    $errorCode = 0;
    if (is_numeric($message)) {
        $errorCode = $message;
        $message = ParserError::getMessageByCode($errorCode);
    }

    if (!$config['debug'] && is_numeric($message)) {
        $json = Helper::request($config['url'] . $config['set_uri'], [
            'id_queue' => $data['id'],
            'error_code' => $errorCode
        ]);
    }

    if ($message == 'Captcha') {
        $captchaCounter++;
        sleep(60*$captchaCounter);
    }

    echo date('H:i:s ') . 'er: ' . $message . ' url:' . $parseUrl;
}

echo PHP_EOL;



