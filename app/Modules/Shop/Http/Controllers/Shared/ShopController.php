<?php

declare(strict_types=1);

namespace App\Modules\Shop\Http\Controllers\Shared;

use App\Helpers\DateTimeHelper;
use App\Http\Controllers\Controller;
use App\Modules\Shop\Models\ShopCategory;
use App\Modules\Shop\Models\ShopProduct;
use App\Modules\Shop\Services\ShopCouponService;
use App\Modules\Shop\Services\ShopService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;

class ShopController extends Controller
{
    public function __construct(
        private readonly ShopService $service,
        private readonly DateTimeHelper $dateTimeHelper

    ) {}
    public function go(Request $request)
    {
        $validated = $request->validate([
            'url' => ['nullable', 'string', 'min:1', 'max:255'],
            'aid' => ['nullable', 'string', 'min:1', 'max:30'],
            'id' => ['nullable', 'integer'],
            'coupon_id' => ['nullable', 'integer', 'min:1'],
            'search' => ['nullable', 'string', 'min:1', 'max:1000'],
        ]);

        $url = $this->service->getGoRedirectUrl($validated, $request);
        return redirect($url);
    }

    public function index(Request $request)
    {
        $validated = $request->validate([
            'page'        => ['nullable', 'integer', 'min:1', 'max:100'],
            'search'      => ['nullable', 'string', 'max:50'],
        ]);

        //$page = $validated['page'] ?? 0;
        $search = $validated['search'] ?? '';

        $products = $this->service->filter(0, 0, $search);
        $popular = null;
        $coupons = null;
        $articles = null;

        if (!$search) {
            $popular = $this->service->getPopular();
            $coupons = $this->service->getMainPageCoupons();
            $articles = $this->service->getMainPageArticles();
        }


        return view('Shop::shop.home', [
            'monthName' => mb_ucfirst($this->dateTimeHelper->getMonthName(intval(date('m')), 'nominative')),
            'epnCategories' => $this->service->getEpnMenu(),
            'popular' => $popular,
            'products' => $products,
            'coupons' => $coupons,
            'articles' => $articles,
            'searchString' => $search,
            'article' => $this->service->getArticleData('home')
        ]);
    }

    public function sitemap(Request $request)
    {

        $query = ShopProduct::query()
            ->select('id', 'hru','created_at')
            ->whereNull('deleted_at')
            ->whereNotIn('category_0', [16002,1309])
            ->orderBy('id', 'desc')
            ->limit(5000);

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
            'epn_category_id' => ['nullable', 'integer'],
            'search'      => ['nullable', 'string', 'max:50'],
        ]);

        $page = $validated['page'] ?? 0;
        $category = $validated['category_id'] ?? 0;
        $epnCategory = $validated['epn_category_id'] ?? 0;
        $search = $validated['search'] ?? '';

        $products = $this->service->filter((int) $page, $category, $search, (int) $epnCategory);

        return view('Shop::shop.more', ['products' => $products]);
    }

    public function category(Request $request, ShopCategory $category, string $categoryHru='')
    {
        $validated = $request->validate([
            'page'        => ['nullable', 'integer', 'min:1', 'max:100'],
            'category_id' => ['nullable', 'integer'],
            'search'      => ['nullable', 'string', 'max:50'],
        ]);

        $page = $validated['page'] ?? 0;
        //$category = $validated['category_id'] ?? 0;
        $search = $validated['search'] ?? '';

        if ($categoryHru != $category['hru']) {
            return redirect()->route('category', ['category' => $category->id_ae, 'categoryHru' => $category->hru], 301);
        }

        $products = $this->service->filter($page, $category->id_ae, $search);

        return view('Shop::shop.category', [
            'products' => $products,
            'category' => $category,
            'searchString' => $search
        ]);
    }

    public function epnCategory(Request $request, string $categoryId, string $categoryHru='')
    {
        $validated = $request->validate([
            'page'        => ['nullable', 'integer', 'min:1', 'max:100'],
            'search'      => ['nullable', 'string', 'max:50'],
        ]);

        $page = $validated['page'] ?? 0;
        $search = $validated['search'] ?? '';

        $categories = config('epn.categories');
        $category = ($key = array_search($categoryId, array_column($categories, 'id'))) !== false ? $categories[$key] : null;
        if (!$category) {
            return redirect()->route('home');
        }
        if ($categoryHru != $category['uri']) {
            return redirect()->route('epnCategory', ['categoryId' => $category['id'], 'categoryHru' => $category['uri']], 301);
        }

        $products = $this->service->filter($page, 0, $search, (int) $categoryId);

        return view('Shop::shop.epn-category', [
            'products' => $products,
            'category' => $category,
            'searchString' => $search,
            'article' => $this->service->getArticleData('epn-'.$category['id'])
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
        /*$currentUrl = url()->current();
        $referrer = $request->header('referer');
        $referrer = strtok($referrer, '?');
        if ($currentUrl === $referrer && !$product['not_found_at']) {
            $product->not_found_at = Carbon::now();
            $product->save();
            return redirect()->route('go', ['aid'=> $product->id_ae]);
        }*/

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

        $attachment = json_decode($product->vk_attachment ?? '', true) ?: [];

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
        $idAe = null;
        if ($lang == 'ali') {
            $html = file_get_contents('https://old.deshevyi.ru/ali/'. $productHru);
            $idAe = str_replace('redirect_to_id_ae=', '', $html);
            $idAe = trim($idAe);
        } else {
            $lang
                ? Storage::append('old.txt', date('Y-m-d H:i:s ') . 'https://old.deshevyi.ru/p/'. $productHru . '/' . $lang)
                : Storage::append('old.txt', date('Y-m-d H:i:s ') . 'https://old.deshevyi.ru/p/'. $productHru);
            $html = $lang
                ? file_get_contents('https://old.deshevyi.ru/p/'. $productHru . '/' . $lang)
                : file_get_contents('https://old.deshevyi.ru/p/'. $productHru);

            if (str_contains($html, 'redirect_to_id_ae=')) {
                $idAe = str_replace('redirect_to_id_ae=', '', $html);
                $idAe = trim($idAe);
            }
        }

        if ($idAe) {
            $product = ShopProduct::query()->where('id_ae', $idAe)->first();
            if ($product) {
                Storage::append('old_ali_found.txt', date('Y-m-d H:i:s ') . 'https://old.deshevyi.ru/ali/'. $productHru . ' ' . $html);
                return redirect()->route('detail', ['product' => $product, 'productHru' => $product->hru], 301);
            } else {
                Storage::append('old_ali_not_found.txt', date('Y-m-d H:i:s ') . 'https://old.deshevyi.ru/ali/'. $productHru . ' ' . $html);
                return redirect()->route('home');
            }
        }

        if (strpos($html, 'page404')) {
            abort(404);
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
        preg_match('/<!--contentstrat-->(.*?)<!--contentend-->/isU', $html, $contentMatches);
        $result['content'] = $contentMatches[1] ?? null;

        preg_match('/<h1>(.*?)<\/h1>/is', $result['content'], $contentMatches);
        $result['h1'] = $contentMatches[1] ?? null;

        $content = str_replace('/img/captcha.php', '#', $result['content']);

        return view('Shop::shop.detailOld', [
            'title' => $result['title'],
            'description' => $result['description'],
            'keywords' => $result['keywords'],
            'content' => $content,
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
        $search = 'фонар';
        if ($selectionName == 'flashlights') {

            $products = $this->service->filter(0, 0, $search);

            return view('Shop::shop.selection', [
                'products' => $products,
                'title' => 'Фонари и запчати для фонарей',
                'searchString' => $search
            ]);
        } else {
            abort(404);
        }
    }

    public function visit(Request $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'goref'        => ['nullable', 'string', 'min:0', 'max:1000'],
        ]);

        $registered = $this->service->registerVisit(
            session()->all(),
            $request->cookie('sid'),
            $request->ip(),
            $validated['goref'] ?? null
        );

        return response()->json(['registered' => $registered]);
    }

    public function addToCart(Request $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'id' => ['required', 'integer']
        ]);

        $id = (int) $validated['id'];
        $cart = Session::get('cart') ?? [];

        if ($id && !in_array($id, $cart) && count($cart) < 100) {
            $cart[] = $id;
            Session::put('cart', $cart);
        }

        return response()
            ->json(['amount' => count($cart)], $status = 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function cart()
    {
        $cart = Session::get('cart') ?? [];

        $query = ShopProduct::query()
            ->select('*')
            ->whereIn('id', $cart)
            ->limit(100);

        $products = $query->get();

        return view('Shop::shop.cart', [
            'products' => $products
        ]);
    }

    public function addToWishlist(Request $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'id' => ['required', 'integer']
        ]);

        $id = (int) $validated['id'];
        $wishlist = Session::get('wishlist') ?? [];

        if ($id && !in_array($id, $wishlist) && count($wishlist) < 100) {
            $wishlist[] = $id;
            Session::put('wishlist', $wishlist);
        }

        return response()
            ->json(['amount' => count($wishlist)], $status = 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function wishlist()
    {
        $wishlist = Session::get('wishlist') ?? [];

        $query = ShopProduct::query()
            ->select('*')
            ->whereIn('id', $wishlist)
            ->limit(100);

        $products = $query->get();

        return view('Shop::shop.wishlist', [
            'products' => $products
        ]);
    }

}
