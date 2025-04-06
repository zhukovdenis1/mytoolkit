<?php

namespace App\Modules\Shop\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Modules\Shop\Models\ShopCategory;
use App\Modules\Shop\Models\ShopProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ShopController extends Controller
{

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
            ->select('id', 'hru','date_add')
            ->where('del', 0)
            ->where('moderated', 1)
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
            ->where('parent_id', 0)
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

        if ($productHru != $product['hru']) {
            return redirect()->route('detail', ['productId' => $product->id, 'productHru' => $product->hru]);
        }

        $size = 200;
        $data = $product['photo'];
        $img = [];
//        for ($i = 0; $i < count($data); $i++)
//        {
//            if (env('APP_DEBUG')) {
//                $img[] = '/img/1.jpg';
//            } else {
//                $img[] = $data[$i];
//            }
//        }

        $attachment = json_decode($product->vk_attachment, 1);

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


}
