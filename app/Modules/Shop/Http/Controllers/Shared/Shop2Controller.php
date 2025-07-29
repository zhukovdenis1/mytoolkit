<?php

declare(strict_types=1);

namespace App\Modules\Shop\Http\Controllers\Shared;

use App\Helpers\DateTimeHelper;
use App\Http\Controllers\Controller;
use App\Modules\Shop\Models\ShopCategory;
use App\Modules\Shop\Models\ShopProduct;
use App\Modules\Shop\Services\ShopService;
use App\Modules\ShopArticle\Models\ShopArticle;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;

class Shop2Controller extends Controller
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
            'page_name' => ['nullable', 'string', 'min:1', 'max:1000'],
        ]);

        $url = $this->service->getGoRedirectUrl($validated, $request);
        return redirect($url);
    }

    public function goHome(Request $request)
    {
        return redirect('/');
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


        $query = ShopProduct::query()
            ->select('a.id', 'a.h1 as title', 'p.title_ae', 'p.photo', 'p.rating', 'p.price', 'p.price_from', 'p.price_to', 'a.uri as hru', 'p.category_0')
            ->from('shop_products as p')
            ->leftJoin('shop_articles as a', 'a.product_id', '=', 'p.id')
            ->where('a.site_id', app()->siteId())
            ->where('a.code', 'like', 'review-%')
            //->whereNotNull('a.product_id')
            ->where('a.published_at', '<=', Carbon::now())
            ->orderByDesc('a.published_at')
            ->limit(24);

        if ($search) {
            $query->where('a.h1', 'like', '%' . $search . '%');
        }


        $popular = $query->get();

        return view('Shop::shop2.home', [
            'monthName' => mb_ucfirst($this->dateTimeHelper->getMonthName(intval(date('m')), 'nominative')),
            'epnCategories' => $this->service->getEpnMenu(),
            'popular' => $popular,
            'products' => $products,
            'coupons' => $coupons,
            'articles' => $articles,
            'searchString' => $search,
            'article' => $this->service->getArticleData('home', (int) app()->siteId())
        ]);
    }

    public function sitemap(Request $request)
    {

        $products = [];

        $productsWithArticles = ShopProduct::query()
            ->select('a.id', 'a.uri as hru', 'a.created_at')
            ->from('shop_products as p')
            ->leftJoin('shop_articles as a', 'a.product_id', '=', 'p.id')
            ->where('a.site_id', app()->siteId())
            ->where('a.code', 'like', 'review-%')
            //->whereNotNull('a.product_id')
            ->where('a.published_at', '<=', Carbon::now())
            ->orderByDesc('a.published_at')
            ->limit(1000)
            ->get();

        $articles = [];

        return view('Shop::shop.sitemap', [
            'products' => $products,
            'articles' => $articles,
            'productsWithArticles' => $productsWithArticles
        ]);
    }
//    public function more(Request $request)
//    {
//        $validated = $request->validate([
//            'page'        => ['nullable', 'integer', 'min:1', 'max:100'],
//            'category_id' => ['nullable', 'integer'],
//            'epn_category_id' => ['nullable', 'integer'],
//            'search'      => ['nullable', 'string', 'max:50'],
//        ]);
//
//        $page = $validated['page'] ?? 0;
//        $category = $validated['category_id'] ?? 0;
//        $epnCategory = $validated['epn_category_id'] ?? 0;
//        $search = $validated['search'] ?? '';
//
//        $products = $this->service->filter((int) $page, (int) $category, $search, (int) $epnCategory);
//
//        return view('Shop::shop.more', ['products' => $products]);
//    }

    public function productMore(Request $request)
    {
        $validated = $request->validate([
            'page'        => ['nullable', 'integer', 'min:1', 'max:100'],
            'product_id'  => ['required', 'integer'],
            'item'        => ['required', 'string']
        ]);

        $page = $validated['page'] ?? 0;
        $productId = $validated['product_id'] ?? 0;
        $item = $validated['item'];

        if ($item == 'reviews') {
            $reviews = $this->service->reviews((int) $page, (int) $productId);
            return view('Shop::shop.review-more', ['reviews' => $reviews]);
        } elseif ($item == 'description') {
            $product = ShopProduct::select('description')->findOrFail($productId);
            return $product->description ?? '';
        } elseif ($item == 'characteristics') {
            $product = ShopProduct::select('characteristics')->findOrFail($productId);
            return $product->characteristics ?? '';
        } else {
            abort(404);
        }
    }

    public function detail(Request $request, ShopArticle $review, string $reviewHru='')
    {
        $product = ShopProduct::findOrFail($review->product_id);

        if (is_string($product->reviews)) {
            $product['reviews'] = json_decode($product->reviews);
        }

        if ($review['uri'] != $reviewHru) {
            return redirect()->route('review.detail', ['review' => $review, 'reviewHru' => $review['uri']], 301);
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

        //$this->service->incViews($request, (int)$product->id);


        return view('Shop::shop.detail', [
                'p' => $product,
                'images' => $img,
                'vkAttachment' => $vkAttachment,
                'recommends' => [],
                //'review' => $article,
                'reviewArticle' => $review ? $this->service->prepareArticleForDisplay($review) : null,
            ]
        );
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
