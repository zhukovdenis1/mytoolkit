<?php

namespace App\Modules\Shop\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Modules\Shop\Models\ShopCategory;
use App\Modules\Shop\Models\ShopProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;

class ShopController extends Controller
{

    public function go(Request $request)
    {
        $validated = $request->validate([
            'url' => ['nullable', 'string', 'min:1', 'max:255'],
            'aid' => ['nullable', 'string', 'min:1', 'max:15'],
            'search' => ['nullable', 'string', 'min:1', 'max:255'],
        ]);

        $url = $validated['url'] ?? '';
        $aliProductId = $validated['aid'] ?? 0;
        $searchText = $validated['search'] ?? '';

        $agent = $_SERVER['HTTP_USER_AGENT'] ?? '';

        if (!(strpos($agent, 'Bot/') || strpos($agent, 'bot/'))) {
            $sText = '';
            if ($searchText && $searchText[0] !== '{') {
                $sTextArr = explode(' ', $searchText);
                $ssTextArr = [];
                for ($i = 0; $i < 4; $i++) $ssTextArr[] = $sTextArr[$i];
                $sText = implode('-', $ssTextArr);
            } elseif ($searchText && $searchText[0] == '{') {
                $sText = $searchText;
            }

            $ip = $_SERVER['REMOTE_ADDR'];

            //mysqli_query($resource, "INSERT INTO ali_product_redirect (site_id,ali_product_id,search_text,url,ip,agent) VALUES ( $siteId,'$aliProductId','$mysqlsText','$referer','$ip','$agent')");

            $redirectUrl = null;

            if ($searchText == '{basket}') {
                $redirectUrl = 'https://aliexpress.ru/cart';
            } elseif ($searchText == '{wishlist}') {
                $redirectUrl = 'https://aliexpress.ru/wishlist';
            } elseif ($searchText == '{login}') {
                $redirectUrl = 'https://login.aliexpress.ru/';
            } elseif (!$aliProductId && $sText) {
                //$redirectUrl = urlencode('http://aliexpress.com/wholesale?SearchText=' . $sText);
                //$redirectUrl = urlencode('https://aliexpress.ru/wholesale?SearchText=' . $sText);
                $redirectUrl = urlencode('https://aliexpress.ru/w/wholesale-' . $sText . '.html');
            } elseif ($aliProductId) {
                //$redirectUrl = 'https://aliexpress.com/item/xxx/'. $aliProductId .'.html';
                $redirectUrl = 'https://aliexpress.ru/item/' . $aliProductId . '.html';
            } elseif ($url) {
                $redirectUrl = $url;
            }

            if (!$redirectUrl) {
                header('Location: /');
                die();
            }

            //header('Location: ' . 'https://shopnow.pub/redirect/cpa/o/sn6o728y02533c8wkahea3zoo0s0qodj/?erid=2SDnjdhZBWB&to=' . $redirectUrl);//krutye-veshi link EPN - d-x.su
            header('Location: ' . 'http://click.deshevyi.ru/redirect/cpa/o/sn6o728y02533c8wkahea3zoo0s0qodj/?erid=2SDnjdhZBWB&to=' . $redirectUrl);//krutye-veshi link EPN - d-x.su

            //header('Location: ' . 'https://alitems.site/g/1e8d11449443646eb20616525dc3e8/?ulp=' . $redirectUrl);//admitad
            //header('Location: ' . 'http://epnredirect.ru/redirect/cpa/o/8a20f237c0ba70728802f3ed17f7c5dc?to=' . $redirectUrl);//krutye-veshi link EPN - d-x.su
            //header('Location: ' . 'http://epnredirect.ru/redirect/cpa/o/7c5d917e448a69c032c6222eef78c780?to=https://ru.aliexpress.com/item/xxx/'. $aliProductId .'.html');//desh-mag
            die();
        } else {
            header('Location: /');
            die();
        }
    }

    public function index(Request $request)
    {
        $validated = $request->validate([
            'page'        => ['nullable', 'integer', 'min:1', 'max:100'],
            'category_id' => ['nullable', 'integer'],
            'search'      => ['nullable', 'string', 'max:50'],
        ]);

        $page = $validated['page'] ?? 0;
        $category = $validated['category_id'] ?? 0;
        $search = $validated['search'] ?? '';

        $products = ShopProduct::filter($page, $category, $search);

        return view('Shop::shop.index', [
            'products' => $products,
            'title' => 'Недорогой интернет-магазин с бесплатной доставкой / DealExtreme на русском языке',
            'category' => $category,
            'search' => $search
        ]);
    }

    public function sitemap(Request $request)
    {

        $query = ShopProduct::query()
            ->select('id', 'hru','created_at')
            ->whereNull('deleted_at')
            ->whereNotIn('category_0', [16002,1309])
            ->orderBy('id', 'desc')
            ->limit(10000);

        $products = $query->get();

        return view('Shop::shop.sitemap', [
            'products' => $products
        ]);
    }
    public function more(Request $request)
    {
        $validated = $request->validate([
            'page'        => ['nullable', 'integer', 'min:1', 'max:100'],
            'category_id' => ['nullable', 'integer'],
            'search'      => ['nullable', 'string', 'max:50'],
        ]);

        $page = $validated['page'] ?? 0;
        $category = $validated['category_id'] ?? 0;
        $search = $validated['search'] ?? '';

        $products = ShopProduct::filter($page, $category, $search);

        return view('Shop::shop.more', ['products' => $products]);
    }

    public function category(Request $request, $categoryId = 0, $categoryHru='')
    {
        $validator = Validator::make(array_replace($request->all(), ['category_id' => $categoryId]), [
            'page'        => ['nullable', 'integer', 'min:1', 'max:100'],
            'category_id' => ['nullable', 'integer'],
            'search'      => ['nullable', 'string', 'max:50'],
        ]);

        $validated = $validator->validated();

        $page = $validated['page'] ?? 0;
        $category = $validated['category_id'] ?? 0;
        $search = $validated['search'] ?? '';

        $categoryData = ShopCategory::query()
            ->select('title','hru','id_ae')
            ->where('id_ae', $category)
            ->limit(1)->first();

        if (is_null($categoryData)) {
            abort(404);
        }

        if ($categoryHru != $categoryData['hru']) {
            return redirect()->route('category', ['categoryId' => $categoryData->id_ae, 'categoryHru' => $categoryData->hru]);
        }

        $products = ShopProduct::filter($page, $category, $search);

        return view('Shop::shop.index', [
            'products' => $products,
            'title' => $categoryData->title . '/ Недорогой интернет магазин' ?? 'Недорогой интернет магазин',
            'category' => $category,
            'search' => $search
        ]);
    }

    public function getCategories(Request $request)
    {
        $query = ShopCategory::query()
            ->select('id_ae', 'title', 'hru')
            ->whereNull('parent_id')
            ->where('hidden', 0)
            ->orderBy('title', 'asc');

        $categories = $query->get();

        return response()
            ->json($categories, $status = 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function detail(Request $request, $productId = 0, $productHru='')
    {
        $validator = Validator::make(array_replace($request->all(), ['productId' => $productId]), [
            'productId'        => ['required', 'integer'],
        ]);

        $validated = $validator->validated();

        $product = ShopProduct::query()->findOrFail($productId);

        if (is_string($product->reviews)) {
            $product['reviews'] = json_decode($product->reviews);
        }

        if ($productHru != $product['hru']) {
            return redirect()->route('detail', ['productId' => $product->id, 'productHru' => $product->hru]);
        }

        $size = 200;
        $data = $product['photo'];
        $img = [];
        for ($i = 0; $i < count($data); $i++)
        {
            $img[] = $data[$i];
            /*if (env('APP_DEBUG')) {
                $img[] = '/img/1.jpg';
            } else {
                $img[] = $data[$i];
            }*/
        }

        $attachment = json_decode($product->vk_attachment, 1) ?: [];

        $vkAttachment = '';
        foreach ($attachment as $a)
        {
            if ($a['type'] == 'photo' && isset($a['photo']['src_big']))
            {
                $vkAttachment .= '<a class="prodImg" rel="gal" href="' . $a['photo']['src_big'] . '"><img src="' . $a['photo']['src'] . '" /></a>';
            }
            elseif ($a['type'] == 'doc' && $a['doc']['ext'] == 'gif')
            {
                $vkAttachment .= '<img src="' . $a['doc']['url'] . '" />';
            }
        }

        return view('Shop::shop.detail', ['p' => $product, 'images' => $img, 'plnk' => 'http://ya.ru', 'vkAttachment' => $vkAttachment, 'recommends' => []]);
    }

    public function aedetail(Request $request, $id_ae = 0)
    {
        $validator = Validator::make(array_replace(['id_ae' => $id_ae]), [
            'id_ae'        => ['required', 'integer'],
        ]);

        $validated = $validator->validated();

        $product = ShopProduct::query()
            ->select('id','hru')
            ->where('id_ae', $id_ae)
            ->limit(1)->first();

        if (is_null($product)) {
            abort(404);
        }

        return redirect()->route('detail', ['productId' => $product->id, 'productHru' => $product->hru]);
    }

    public function robots()
    {
        echo 'User-agent: *' . PHP_EOL
            . 'Disallow: /css/' . PHP_EOL
            . 'Disallow: /img/' . PHP_EOL
            . 'Disallow: /js/' . PHP_EOL
            . 'Disallow: /more*' . PHP_EOL
            . 'Disallow: /?search=*' . PHP_EOL
            . 'Disallow: /?r=*';
        die;

    }

    public function newOrder(Request $request)
    {
        $data = (json_encode($request->all(), JSON_UNESCAPED_UNICODE));
        Storage::append('orders.txt', date('Y-m-d H:i:s ') . $data);
//        Mail::raw(date('Y-m-d H:i:s ') . $data, function ($message) {
//            $message->to('zd1@list.ru')
//                ->subject('Новый заказ');
//        });
        Mail::send([], [], function ($message) use ($data) {
            $message->to('zd1@list.ru')
                ->subject('Новый заказ')
                ->text(date('Y-m-d H:i:s ') . $data); // или ->html('<p>HTML content</p>')
        });
        return response()->json($data);
    }


}
