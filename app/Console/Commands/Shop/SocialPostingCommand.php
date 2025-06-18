<?php

/**
 * Постинг объявлений за прошедшие сутки в группы
 * https://snipp.ru/php/vk-posting#link-post-s-kartinkoy
 * access_token получается по ссылке: https://oauth.vk.com/authorize?client_id=XXXXXX&scope=wall,photos,offline&redirect_uri=http://api.vk.com/blank.html&display=wap&response_type=token
 * CLient_ID Здесь: https://vk.com/editapp?id=6094217&section=options (приложение должно быть включено: https://vk.com/apps?act=manage)
 * Документация wall.post https://dev.vk.com/ru/method/wall.post
 *
 * $postData = array(
 * array('group' => 56266642, 'category' => ModelAliProduct::CAT_KRUTYEVESHI),
 * array('group' => 177990475, 'category' => ModelAliProduct::CAT_WOMEN),
 * array('group' => 177988564, 'category' => ModelAliProduct::CAT_CHILDREN),
 * array('group' => 177992765, 'category' => ModelAliProduct::CAT_MEN),
 * array('group' => 177993066, 'category' => ModelAliProduct::CAT_FISHER),
 * array('group' => 177989390, 'category' => ModelAliProduct::CAT_AUTO),
 * array('group' => 177993235, 'category' => ModelAliProduct::CAT_HANDMADE),
 * );
 *
 */

namespace App\Console\Commands\Shop;

use App\Helpers\StringHelper;
use App\Helpers\VkHelper;
use App\Modules\Shop\Models\ShopCoupon;
use App\Modules\Shop\Models\ShopProduct;
use App\Modules\Shop\Models\ShopProductParseQueue;
use App\Modules\Shop\Models\ShopVkGroup;
use Carbon\Carbon;
use CURLFile;
use Illuminate\Console\Command;
use App\Modules\Shop\Services\EpnApiClient;
use Illuminate\Support\Facades\DB;
use Laravel\Telescope\Http\Controllers\QueriesController;

class SocialPostingCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shop:post';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Post to social media';


    public function __construct(private readonly VkHelper $vkHelper) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $accessToken = 'vk1.a.lhwj6YirK9ZqNmYqEyN4rmxYkF_8k6NN34wep0DZVK9N-nMuOzCtfTP1v2Wy0dhJc0_AlN0bZliHHbDofjfmcORXgZGbNV7t9YID_sDxJLHaiqiyrBMBy5VKJ4V0A9c9cL9ii4TyjhFjrhaP3T3qzmYjPrAWIwFRksi7EmtM0vFAevQJQj1HplPGhk263JaBlpgN8gSVA4cQaVF1Eolj7w';
        $groupId = 56266642;

        $output = '';

        $product = ShopProduct::query()
            //->where('source', 'vk')
            ->whereNull('posted_at')
            ->orderByDesc('epn_month_income')
            //->orderByDesc('id')
            ->first();

        if ($product) {
            echo '<a href="https://vk.com/club' . $groupId . '" target="_blank">go to group</a><br><br />';
            $result = $this->postProduct($product, $accessToken, $groupId);
            var_dump($result);
        }

        $coupon = ShopCoupon::query()
            ->whereNotNull('url')
            ->whereNull('posted_at')
            ->where('date_to', '>=', Carbon::now())
            ->orderByDesc('id')
            ->first();

        if ($coupon) {
            echo '<a href="https://vk.com/club' . $groupId . '" target="_blank">go to group</a><br><br />';
            $result = $this->postCoupon($coupon, $accessToken, $groupId);
            var_dump($result);
        }

        //$output = "Group: vk.com/public" . $group->id . " Income: " . count($data) . "; Inserted: " . $insertCount . ";\n";

        $this->info($output);
        return 0; // Возвращаем 0, если команда выполнена успешно
    }

    private function postProduct(ShopProduct $product, string $accessToken, int $groupId)
    {
        $photos = $product['photo'];
        $fullServerPathToImage = $photos[0];

        if (!$fullServerPathToImage) {
            ShopProduct::where('id', $product['id'])
                ->update([
                    'posted_at' => Carbon::now()
                ]);
            $this->info ('no photo ID='. $product['id']);
            return [];
        }

        $response = $this->vkHelper->apiRequest(
            'photos.getWallUploadServer',
            ['v' => VkHelper::VERSION, 'group_id' => $groupId, 'access_token' => $accessToken]
        );

        $uploadUrl = $response['response']['upload_url'];

        file_put_contents(app()->basePath() . '/tmp/tmp.jpg', file_get_contents($fullServerPathToImage));

        $i = 0;
        $responses = array();
        foreach ($photos as $p) {
            $i++;
            if ($i < 7) // максимум 7
            {
                file_put_contents(app()->basePath() . '/tmp/tmp' . $i . '.jpg', file_get_contents($p));

                $ch = curl_init($uploadUrl);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, array('photo' => new CURLFile(app()->basePath() . '/tmp/tmp' . $i . '.jpg')));
                //curl_setopt($ch, CURLOPT_POSTFIELDS, array('photo' =>curl_file_create(dirname(__FILE__) . '/tmp.jpg')));
                curl_setopt($ch, CURLOPT_HEADER, 0);
                $response = curl_exec($ch);
                $response = json_decode($response, 1);

                $response = $this->vkHelper->apiRequest(
                    'photos.saveWallPhoto',
                    [
                        'v' => VkHelper::VERSION,
                        'group_id' => $groupId,
                        'photo' => $response['photo'],
                        'server' => $response['server'],
                        'hash' => $response['hash'],
                        'access_token' => $accessToken
                    ]
                );

                $responses[] = $response;
            }
        }

        $hashtag = '#алиэкспресс';

//        $titleParts = explode(' ', $product[ModelAliProduct::TITLE_FIELD]);
//        if ($titleParts[0])
//        {
//            if ($hashtag === '') $hashtag = PHP_EOL;
//            $hashtag .= '#' . str_replace(array(' ','-','.',','), '', Helper::strToLower($titleParts[0]));
//        }
//
//        foreach ($categories as $c)
//        {
//            if ($hashtag === '') $hashtag = PHP_EOL;
//            $cParts = explode(' ', $c[ModelAliCategory::TITLE_FIELD]);
//            $hashtag .= ' #'. str_replace(array(' ','-','.',','), '', Helper::strToLower($cParts[0]));
//            if ($cParts[0] !== $c[ModelAliCategory::TITLE_FIELD])
//                $hashtag .= ' #'. str_replace(array(' ','-','.',','), '', Helper::strToLower($c[ModelAliCategory::TITLE_FIELD]));
//        }
//        if (helper::strLength($product[ModelAliProduct::TITLE_FIELD]) < 30)
//        {
//            if ($hashtag === '') $hashtag = PHP_EOL;
//            $hashtag .= ' #' . str_replace(array(' ','-','.',','), '', Helper::strToLower($product[ModelAliProduct::TITLE_FIELD]));
//        }

        //$productUrl = route('detail', ['product' => $product, 'productHru' => $product->hru]);
        $productUrl = 'https://deshevyi.ru/p-' . $product->id . '/' . $product->hru;

        //$message = $product['title_ae'] . PHP_EOL . 'Цена: ' . $product->price . PHP_EOL . $productUrl . $hashtag;
        $vkMessage = $product['title_ae'] . PHP_EOL . 'Цена: ' . $product->price . ' руб' . PHP_EOL . $hashtag . PHP_EOL . '↓↓↓ подробнее по ссылке ниже ↓↓↓';
        $tgMessage = '<b>' . $product['title_ae'] . '</b>' . PHP_EOL . PHP_EOL
            . 'Цена: <i>' . $product['price'] . ' руб.</i>' . PHP_EOL . PHP_EOL
            . '<a href="' . $productUrl . '">Подробнее</a>';

        //$result = postToGroup($groupId, $message, $photoAttachment . ',' . $productUrl);

        $pAttachment = $productUrl;
        foreach ($responses as $r) {
            $pAttachment .= ',photo' . $r['response'][0]['owner_id'] . '_'.$r['response'][0]['id'];
        }


        $result = $this->postToGroup($groupId, $vkMessage, $pAttachment );

        //if ($pd['category'] == ModelAliProduct::CAT_KRUTYEVESHI || empty($pd['category']))
        {
            $result = $this->postToTelegram($tgMessage, $photos);
        }


        //if ($result['response']['post_id'])
        {
            //ModelAliProduct::editByKey(array(ModelAliProduct::VK_PUBLISHED_FIELD => $result['response']['post_id']), $product[ModelAliProduct::ID_FIELD]);
        }

        ShopProduct::where('id', $product['id'])
            ->update([
                'posted_at' => Carbon::now()
            ]);

        return $result;
    }

    private function postCoupon(ShopCoupon $coupon, string $accessToken, int $groupId)
    {
        $response = $this->vkHelper->apiRequest(
            'photos.getWallUploadServer',
            ['v' => VkHelper::VERSION, 'group_id' => $groupId, 'access_token' => $accessToken]
        );

        $uploadUrl = $response['response']['upload_url'];


        $hashtag = '#алиэкспресс #промокоды #купоны';

        $url = 'https://deshevyi.ru/coupons/' . $coupon->id . '/' . $coupon->uri;

        $vkMessage = $coupon->title . PHP_EOL . $coupon->description. PHP_EOL . $hashtag . PHP_EOL . '↓↓↓ подробнее по ссылке ниже ↓↓↓';

        $tgMessage = "<b>{$coupon->title}</b>"
            . PHP_EOL. PHP_EOL . $coupon->description
            . PHP_EOL . '<a href="' . $url . '">Подробнее</a>          <a href="https://deshevyi.ru/coupons">Все купоны</a>';

        $pAttachment = $url;

        $result = $this->postToGroup($groupId, $vkMessage, $pAttachment );

        //if ($pd['category'] == ModelAliProduct::CAT_KRUTYEVESHI || empty($pd['category']))
        {
            $result = $this->postToTelegram($tgMessage, []);
        }


        //if ($result['response']['post_id'])
        {
            //ModelAliProduct::editByKey(array(ModelAliProduct::VK_PUBLISHED_FIELD => $result['response']['post_id']), $product[ModelAliProduct::ID_FIELD]);
        }

        ShopCoupon::where('id', $coupon['id'])
            ->update([
                'posted_at' => Carbon::now()
            ]);

        return $result;
    }

    private function postToGroup($groupId, $message, $attachments)
    {
        //$message = 'test';
        //$groupId = 146508796;
        //$href = 'http://vkarenda.ru';

        $params = array(
            'v' => VkHelper::VERSION,
            'owner_id' => '-' . $groupId,
            'from_group' => 1,
            'access_token' =>'vk1.a.lhwj6YirK9ZqNmYqEyN4rmxYkF_8k6NN34wep0DZVK9N-nMuOzCtfTP1v2Wy0dhJc0_AlN0bZliHHbDofjfmcORXgZGbNV7t9YID_sDxJLHaiqiyrBMBy5VKJ4V0A9c9cL9ii4TyjhFjrhaP3T3qzmYjPrAWIwFRksi7EmtM0vFAevQJQj1HplPGhk263JaBlpgN8gSVA4cQaVF1Eolj7w',
            'message' => $message,
            //'attachment' => $href,
            'from_group' => 1,
            'attachments' => $attachments
        );

        $url = 'https://api.vk.com/method/wall.post';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
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
            $result = json_decode($result, 1);
        }

        return $result;
    }

    private function postToTelegram(string $message, array $photos)
    {
        $botToken = '7983496183:AAFVULz9fk7FgiF9t3kkzh1wdZyFov4W15E';
        $chatId = '-1001177719353';

        if ($photos) {
            $url = "https://api.telegram.org/bot{$botToken}/sendMediaGroup";

            // Подготовка массива медиа
            $media = [];
            foreach ($photos as $index => $photoUrl) {
                $media[] = [
                    'type' => 'photo',
                    'media' => $photoUrl,
                    'caption' => $message,
                    'text' => $message,
                    'parse_mode' => 'HTML'
                ];
            }

            $data = [
                'chat_id' => $chatId,
                'media' => json_encode($media)
            ];
        } else {
            $url = "https://api.telegram.org/bot{$botToken}/sendMessage";

            $data = [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'HTML',
                'disable_web_page_preview' => true
            ];
        }


        $options = [
            'http' => [
                'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data)
            ]
        ];

        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        return $result;
    }




}
