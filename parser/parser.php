<?php

require_once 'Helper.php';
require_once 'ParseError.php';

$config = include 'config.php';

//if (!$config['debug']) sleep(mt_rand(0,25));

$parseUrl = '';
$data = ['id' => 1];//for debug case
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

        $data = json_decode($json, true);
        if (empty($data['data'])) {
            throw new Exception('Empty parse queue');
        }
        $data = $data['data'];

        $queueItemId = $data['id'];
        if ($data['source'] == 'epn_hot') {
            $parseUrl = $data['info']['attributes']['directUrl'];
        } elseif ($data['source'] = 'epn_top') {
            $parseUrl = 'https://aliexpress.ru/item/'.$data['id_ae'].'.html';
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

    $aliData = array_merge($baseData, $extraData);

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
        $aliData = array_merge($aliData, $extra2Data);
    }

    $validateErrors = Helper::validateErrors($aliData);

    if ($validateErrors) {
        echo implode(PHP_EOL, $validateErrors) . PHP_EOL;
        throw new Exception(ParserError::ValidationError->value);
    }

    if (empty($data['id'])) {
        throw new Exception(ParserError::EmptyIdQueue->value);
    }

    if ($config['debug']) {
        var_dump(['id_queue' => $data['id'], 'data' => $aliData, 'brcr' => $brcr, 'version' => Helper::$version]);
    } else {
        $response = Helper::request($config['url'] . $config['set_uri'],
            ['id_queue' => $data['id'], 'data' => $aliData, 'brcr' => $brcr, 'version' => Helper::$version]
        );
//        var_dump($data['id']);
        file_put_contents('response.html', $response);

        $responseData = json_decode($response, true);

        echo date('H:i:s ') . 'v'. Helper::$version . ' ok: ' . $config['url_shop'] . '/p-' . $responseData['data']['product']['id'] . '/ ; id_queue='.$data['id'];
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
            'id_queue' => $data['id'],
            'error_code' => $errorCode,
            'version' => Helper::$version,
        ]);
    }

    echo date('H:i:s ') . 'v'. Helper::$version. ' er: ' . $message . ' url:' . $parseUrl;
}

echo PHP_EOL;
