<?php

declare(strict_types=1);

namespace App\Helpers;

class VkHelper
{
    const VERSION = '5.81';
    //const MESSAGE_SEND_INTERVAL = 20;//sec
    //const WALL_GET_INTERVAL = 30;//sec

    /*
     *
     */
    public function getWall(string $groupId, int $postQuantity): array
    {
        $vkParams = array(
            'v' => self::VERSION,
            'owner_id' => '-' . $groupId,
            'count' => $postQuantity,
            'extended' => 1,
            'fields'=>'members_count');

        $response = self::apiRequest('wall.get', $vkParams);

        //echo '<pre>';var_dump($response);die;
        $result = array('items' => array());

        foreach ($response["response"]["items"] as $w)
        {
            if ($w["marked_as_ads"] == 0 /*&& $w["is_pinned"] == 0*/)
            {
                $result['items'][] = array(
                    'id' => $w['id'],
                    'text' => $w['text'],
                    'attachments' => $w['attachments']
                );
            }
        }

        return $result;
    }

    /*
     *
     */
    public function apiRequest($method, $params, $useUserKey = false)
    {
        $params['v'] = self::VERSION;

        if ($useUserKey)
        {
            //todo
        }
        elseif (!isset($params['access_token']))
        {
            $params['access_token'] = config('social.vk.key');//service key
        }

        $url = 'https://api.vk.com/method/' . $method;
        /*$params = array_merge($params, array(
            'access_token' =>'6307d5b67b4366f1b012a0d616e18ecb331...'
        ));*/

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/535.19 (KHTML, like Gecko) Chrome/18.0.1025.142 Safari/535.19");//Юзер агент
        curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
        if ($params)
        {
            $postString = http_build_query($params);
            curl_setopt ($ch, CURLOPT_POSTFIELDS, $postString);
        }

        $result = curl_exec($ch);

        curl_close($ch);

        if ($result)
        {
            $result = json_decode($result, true);
        }

        return $result;
    }
}
