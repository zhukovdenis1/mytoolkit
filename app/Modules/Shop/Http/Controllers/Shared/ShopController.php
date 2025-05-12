<?php

namespace App\Modules\Shop\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Modules\Shop\Models\ShopCategory;
use App\Modules\Shop\Models\ShopProduct;
use App\Modules\Shop\Services\ShopService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;

class ShopController extends Controller
{
    public function __construct(private readonly ShopService $service) {}
    public function go(Request $request)
    {
        $validated = $request->validate([
            'url' => ['nullable', 'string', 'min:1', 'max:255'],
            'aid' => ['nullable', 'string', 'min:1', 'max:15'],
            'coupon_id' => ['nullable', 'integer', 'min:1'],
            'search' => ['nullable', 'string', 'min:1', 'max:1000'],
            'title' => ['nullable', 'string', 'min:1', 'max:1000'],
        ]);

        return redirect($this->service->getGoRedirectUrl($validated, $request));
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
            'search' => $search,
            'article' => $this->service->getArticleData()
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

    public function category(Request $request, ShopCategory $category, string $categoryHru='')
    {
//        $validator = Validator::make(array_replace($request->all(), ['category_id' => $categoryId]), [
//            'page'        => ['nullable', 'integer', 'min:1', 'max:100'],
//            'category_id' => ['nullable', 'integer'],
//            'search'      => ['nullable', 'string', 'max:50'],
//        ]);
        //$validated = $validator->validated();

        $validated = $request->validate([
            'page'        => ['nullable', 'integer', 'min:1', 'max:100'],
            'category_id' => ['nullable', 'integer'],
            'search'      => ['nullable', 'string', 'max:50'],
        ]);

        $page = $validated['page'] ?? 0;
        //$category = $validated['category_id'] ?? 0;
        $search = $validated['search'] ?? '';

//        $categoryData = ShopCategory::query()
//            ->select('title','hru','id_ae')
//            ->where('id_ae', $category)
//            ->limit(1)->first();

//        if (is_null($categoryData)) {
//            abort(404);
//        }

        if ($categoryHru != $category['hru']) {
            return redirect()->route('category', ['category' => $category->id_ae, 'categoryHru' => $category->hru], 301);
        }

        $products = ShopProduct::filter($page, $category->id_ae, $search);

        return view('Shop::shop.index', [
            'products' => $products,
            'title' => $category->title . '/ Недорогой интернет магазин' ?? 'Недорогой интернет магазин',
            'category' => $category->id_ae,
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

    public function detail(Request $request, ShopProduct $product, string $productHru='')
    {
//        $validator = Validator::make(array_replace($request->all(), ['productId' => $productId]), [
//            'productId'        => ['required', 'integer'],
//        ]);
//
//        $validated = $validator->validated();
//
//        $product = ShopProduct::query()->findOrFail($productId);

        if (is_string($product->reviews)) {
            $product['reviews'] = json_decode($product->reviews);
        }

        //var_dump($_SERVER['HTTP_REFERER']);die;
        $currentUrl = url()->current();
        $referrer = $request->header('referer');
        $referrer = strtok($referrer, '?');
        if ($currentUrl === $referrer && !$product['not_found_at']) {
            $product->not_found_at = Carbon::now();
            $product->save();
            return redirect()->route('go', ['search'=> $product->title]);
        }

        if ($productHru != $product['hru']) {
            return redirect()->route('detail', ['product' => $product, 'productHru' => $product->hru], 301);
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

        $this->service->incViews($request, (int)$product->id);


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

        return redirect()->route('detail', ['product' => $product, 'productHru' => $product->hru], 301);
    }

    public function robots()
    {
        echo 'User-agent: *' . PHP_EOL
            . 'Disallow: /css/' . PHP_EOL
            . 'Disallow: /img/' . PHP_EOL
            . 'Disallow: /js/' . PHP_EOL
            . 'Disallow: /more*' . PHP_EOL
            . 'Disallow: /?search=*' . PHP_EOL
            . 'Disallow: /go?*' . PHP_EOL
            . 'Disallow: /coupons?page=*' . PHP_EOL
            . 'Disallow: /?r=*';
        die;

    }

    public function newOrder(Request $request)
    {
        $data = (json_encode($request->all(), JSON_UNESCAPED_UNICODE));
        Storage::append('orders.txt', date('Y-m-d H:i:s ') . $data);
        Mail::raw(date('Y-m-d H:i:s ') . $data, function ($message) {
            $message->to('zd1@list.ru')
                ->subject('Новый заказ');
        });
        Mail::send([], [], function ($message) use ($data) {
            $message->to('zd1@list.ru')
                ->subject('Новый заказ')
                ->text(date('Y-m-d H:i:s ') . $data); // или ->html('<p>HTML content</p>')
        });
        return response()->json($data);
    }

    public function oldDetail(Request $request, $productHru, $lang = null)
    {
        if ($lang == 'ali') {
            $html = file_get_contents('https://old.deshevyi.ru/ali/'. $productHru);
            $idAe = str_replace('redirect_to_id_ae=', '', $html);
            $idAe = trim($idAe);
            $product = ShopProduct::query()->where('id_ae', $idAe)->first();
            if ($product) {
                Storage::append('old_ali_found.txt', date('Y-m-d H:i:s ') . 'https://old.deshevyi.ru/ali/'. $productHru . ' ' . $html);
                return redirect()->route('detail', ['product' => $product, 'productHru' => $product->hru], 301);
            } else {
                Storage::append('old_ali_not_found.txt', date('Y-m-d H:i:s ') . 'https://old.deshevyi.ru/ali/'. $productHru . ' ' . $html);
                return redirect()->route('home');
            }
        } else {
            $lang
                ? Storage::append('old.txt', date('Y-m-d H:i:s ') . 'https://old.deshevyi.ru/p/'. $productHru . '/' . $lang)
                : Storage::append('old.txt', date('Y-m-d H:i:s ') . 'https://old.deshevyi.ru/p/'. $productHru);
            $html = $lang
                ? file_get_contents('https://old.deshevyi.ru/p/'. $productHru . '/' . $lang)
                : file_get_contents('https://old.deshevyi.ru/p/'. $productHru);
        }


        $result = [];

        // Извлечение содержимого тега <title>
        preg_match('/<title>(.*?)<\/title>/is', $html, $titleMatches);
        $result['title'] = $titleMatches[1] ?? null;

        // Извлечение content из meta description
        preg_match('/<meta\s+name="description"\s+content="(.*?)"\s*\/?>/is', $html, $descMatches);
        $result['description'] = $descMatches[1] ?? null;

        // Извлечение content из meta keywords
        preg_match('/<meta\s+name="keywords"\s+content="(.*?)"\s*\/?>/is', $html, $keywordsMatches);
        $result['keywords'] = $keywordsMatches[1] ?? null;

        // Извлечение содержимого div с id="ru-title"
        preg_match('/<!--contentstrat-->(.*?)<!--contentend-->/is', $html, $contentMatches);
        $result['content'] = $contentMatches[1] ?? null;

        preg_match('/<h1>(.*?)<\/h1>/is', $result['content'], $contentMatches);
        $result['h1'] = $contentMatches[1] ?? null;

        return view('Shop::shop.detailOld', [
            'title' => $result['title'],
            'description' => $result['description'],
            'keywords' => $result['keywords'],
            'content' => $result['content'],
            'h1' => $result['h1']
        ]);


    }

    public function oldCategory(Request $request, $categoryHru, $categoryHru2 = '', $categoryHru3 = '')
    {
        Storage::append('old_category.txt', date('Y-m-d H:i:s ') . "https://old.deshevyi.ru/c/$categoryHru/$categoryHru2/$categoryHru3");
        return redirect()->route('home');
    }

    public function selection(string $selectionName)
    {
        if ($selectionName == 'flashlights') {
            $products = ShopProduct::filter(0, 0, 'фонар');

            return view('Shop::shop.index', [
                'products' => $products,
                'title' => 'Фонари и запчати для фонарей / Недорогой интернет-магазин',
                'category' => 0,
                'search' => '',
                'article' => null
            ]);
        } else {
            abort(404);
        }
    }
}
