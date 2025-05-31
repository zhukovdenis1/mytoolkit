<?php

namespace App\Console\Commands\Shop;

use App\Helpers\StringHelper;
use App\Helpers\VkHelper;
use App\Modules\Shop\Models\ShopCoupon;
use App\Modules\Shop\Models\ShopProductParseQueue;
use App\Modules\Shop\Models\ShopVkGroup;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Modules\Shop\Services\EpnApiClient;
use Illuminate\Support\Facades\DB;
use Laravel\Telescope\Http\Controllers\QueriesController;

class ParseVkGroupsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shop:parseVkGroups';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';


//    public function handle()
//    {
//        $name = $this->ask('Как вас зовут?');
//        $this->info("Привет, $name!");
//
//        if ($this->confirm('Хотите продолжить?')) {
//            $this->info('Продолжаем...');
//            // Дополнительная логика
//        } else {
//            $this->error('Действие отменено.');
//        }
//    }

    public function __construct(private readonly VkHelper $vkHelper) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $output = '';

        $group = ShopVkGroup::query()
            //->where('parsed_at', '<', Carbon::now()->subDay()) // Проверяем что прошло больше дня
            ->orderBy('parsed_at', 'asc')
            ->first();

        if (!$group) return 0;

        $data = $this->parseVkGroups($group);
        $insertCount = 0;

        if ($data) {
            $insertCount = $this->addProductsToParseQueue($data, $group->id);
        }

        $output = "Group: vk.com/public" . $group->id . " Income: " . count($data) . "; Inserted: " . $insertCount . ";\n";

        $this->info($output);
        return 0; // Возвращаем 0, если команда выполнена успешно
    }

    private function parseVkGroups(ShopVkGroup $group): ?array
    {
        /*try {*/
        $result = $this->vkHelper->getWall($group->id, 10);

        $vkGroupParseData = [];

        foreach ($result['items'] as $w) {
            if (method_exists($this,'groupHandler' . $group->id)) {
                $functionName = 'groupHandler'.$group->id;
                $vkGroupParseData[] = $functionName($w, $w['text']);
            } else {
                $vkGroupParseData[] = $this->groupHandlerDefault($w, $w['text']);
            }
        }

        return $vkGroupParseData;
    }



    private function addProductsToParseQueue(array $products, int $vkGroupId): int
    {
        $chunkSize = 50;
        $insertedCount = 0;


        foreach (array_chunk($products, $chunkSize) as $chunk) {
            $rows = array_map(fn($item) => [
                'source' => 'vk',
                'important' => 0,
                'vk_group_id' => $vkGroupId,
                'vk_post_id' => $item['post_id'],
                'info' => json_encode($item, JSON_UNESCAPED_UNICODE),
                'created_at' => Carbon::now(),
            ], $chunk);

            $insertedCount += ShopProductParseQueue::insertOrIgnore($rows);
        }

        return $insertedCount;

    }

    /**
     * Ищем ссылки в вк постах
     * @param $postContent
     */
    public function findPostLinks($postContent)
    {
        preg_match_all('/(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w\.-]*)*\/?/', $postContent, $matches);
        return $matches;
    }

    /*
     *
     */
    private function groupHandlerDefault(array $result, string $text): array
    {
        $matches = $this->findPostLinks($text);

        $url = '';
        $description = '';

        $href = '';
        if (count($matches[0]))
        {
            $href = $matches[0][0];
        }
        else
        {
            foreach ($result['attachments'] as $a)
            {
                if ($a['type'] == 'link')
                {
                    $href = $a['link']['url'];
                }
            }
        }

        if ($href)
        {
            $url = trim($href);
            $titleArr = explode($url, $text);$title = $titleArr[0];
            $title = mb_ucfirst($title);
            $title = str_replace($matches[0], '', $title);
            $title = str_replace('- ', '', $title);
            $title = str_replace('—', '', $title);
            $title = preg_replace('/#.*?\s/ ', '', $title);
            $title = strip_tags($title);
            $title = trim($title);
            $description = '';

            if (mb_strlen($title) > 100)
            {
                $description = $title;
                $title = '';
            }
        }

        return [
            'post_id' => $result['id'],
            'url' => $url,
            'title' => $this->removeEmoji($title ?? ''),
            'description' => $this->removeEmoji($description)
        ];
    }

    /*
     * Стильный Китай: реальные отзывы. Женское сообщество
     */
    /*private function groupHandler126200762($result, $text)
    {
        $matches = $this->findPostLinks($text);

        $textStrings = explode(PHP_EOL, $text);

        /* ПРИМЕР:
         *  0. Плотные джинсы с высокой талией
            1. Цена: от 1378 до 1443 руб. ($18.22) | 1414 заказов
            2.
            3. Отлично сели на параметры 85-65-88. Размер S. Качество отличное! Плотный хороший джинс. Спасибо продавцу!
            4.
            5. Заказать на Алиэкспресс: http://ali.pub/50p6zs

        if (count($textStrings > 5))
        {
            $title = trim($textStrings[0]);
            unset($textStrings[0]);
            unset($textStrings[1]);
            array_pop($textStrings);
            $description = trim(implode($textStrings));
        }

        $url = '';

        if (count($matches[0]))
        {
            $url = $matches[0][0];
        }

        return array(
            'url' => $url,
            'title' => $this->removeEmoji($title),
            'description' => $this->removeEmoji($description)
        );
    }

    /*
     *
     */
    /*private function groupHandler93943373($result, $text)
    {
        $title = $this->removeEmoji($text);
        $href = '';

        foreach ($result['attachments'] as $a)
        {
            if ($a['type'] == 'photo')
            {
                $text = $a['photo']['text'];
                $links = $this->findPostLinks($text);
                if (isset($links[0][0]))
                {
                    $href = $links[0][0];
                }
            }
            if ($a['type'] == 'link')
            {
                $href = $a['link']['url'];
            }
        }

        return array(
            'url' => $href,
            'title' => $title,
            'description' => ''
        );
    }*/

    /*
 *
 */
    private function removeEmoji(string $string): array|string|null
    {
        //$string = preg_replace('/[^\x{0000}-\x{FFFF}]/u', '', $string);
        //return $string;

        // Match Emoticons
        $regex_emoticons = '/[\x{1F600}-\x{1F64F}]/u';
        $clear_string = preg_replace($regex_emoticons, '', $string);

        // Match Miscellaneous Symbols and Pictographs
        $regex_symbols = '/[\x{1F300}-\x{1F5FF}]/u';
        $clear_string = preg_replace($regex_symbols, '', $clear_string);

        // Match Transport And Map Symbols
        $regex_transport = '/[\x{1F680}-\x{1F6FF}]/u';
        $clear_string = preg_replace($regex_transport, '', $clear_string);

        // Match Miscellaneous Symbols
        $regex_misc = '/[\x{2600}-\x{26FF}]/u';
        $clear_string = preg_replace($regex_misc, '', $clear_string);

        // Match Dingbats
        $regex_dingbats = '/[\x{2700}-\x{27BF}]/u';
        $clear_string = preg_replace($regex_dingbats, '', $clear_string);

        return $clear_string;
    }



}
