<?php

declare(strict_types=1);

use App\Modules\Shop\Models\ShopCategory;
use App\Modules\Shop\Models\ShopCoupon;
use App\Modules\Shop\Models\ShopProduct;
use App\Modules\ShopArticle\Models\ShopArticle;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;
use App\Helpers\ShopArticleHelper;

Breadcrumbs::for('home', function (BreadcrumbTrail $trail): void {
    $trail->push('Главная', route('home'));
});

Breadcrumbs::for('cart', function (BreadcrumbTrail $trail): void {
    $trail->parent('home');
    $trail->push('Корзина', route('cart'));
});

Breadcrumbs::for('wishlist', function (BreadcrumbTrail $trail): void {
    $trail->parent('home');
    $trail->push('Избранное', route('wishlist'));
});

Breadcrumbs::for('oldDetail', function (BreadcrumbTrail $trail): void {
    $trail->parent('home');
    $trail->push('Страница товара', route('home'));
});

Breadcrumbs::for('coupons', function (BreadcrumbTrail $trail): void {
    $trail->parent('home');
    $trail->push('Купоны', route('coupons'));
});

Breadcrumbs::for('coupon.detail', function (BreadcrumbTrail $trail, ShopCoupon|string $coupon): void {
    if (is_string($coupon)) {
        return;
    }
    $trail->parent('coupons');
    $trail->push($coupon->title, route('home'));
});

Breadcrumbs::for('articles', function (BreadcrumbTrail $trail): void {
    $trail->parent('home');
    $trail->push('Инфо', route('articles'));
});

Breadcrumbs::for('article.detail', function (BreadcrumbTrail $trail, ShopArticle|string $article): void {
    if (is_string($article)) {
        return;
    }
    $helper = app(ShopArticleHelper::class);
    $name = $article->name ?? '';
    $name = $helper->replace($name);
    $trail->parent('articles');
    $trail->push($name, route('home'));

});

Breadcrumbs::for('epnCategory', function (BreadcrumbTrail $trail, $categoryId): void {

    $categories = config('epn.categories');
    $category = ($key = array_search($categoryId, array_column($categories, 'id'))) !== false ? $categories[$key] : null;
    $trail->parent('home');
    $trail->push($category['name'], '');

});

Breadcrumbs::for('category', function (BreadcrumbTrail $trail, $category): void {
//    $query = ShopCategory::query()
//        ->select('id_ae', 'title', 'hru', 'parents')
//        ->where('id_ae', $categoryId)
//        ->limit(1);
//    $category = $query->first();

    if (is_string($category)) {
        return;
    }

    $trail->parent('home');
    $trail->push($category->title, '');

});


Breadcrumbs::for('detail', function (BreadcrumbTrail $trail, ShopProduct|string $product): void {
    if (is_string($product)) {
        return;
    }
    //$pd = ShopProduct::query()->find($productId, ['category_0','category_1','category_2','category_3', 'title']);
    $categories = [$product->category_0, $product->category_1, $product->category_2, $product->category_3];

    $trail->parent('home');

    if ($product->epn_category_id) {
        $categories = config('epn.categories');
        foreach ($categories as $category) {
            if ($category['id'] == $product->epn_category_id) {
                $trail->push(
                    $category['name'],
                    route(
                        'epnCategory',
                        ['categoryId' => $product->epn_category_id, 'categoryHru' => $category['uri']]
                    )
                );
            }
        }
    } else {
        $categories = ShopCategory::query()
            ->select('id_ae', 'title', 'hru')
            ->whereIn('id_ae', $categories)
            ->limit(4)
            ->get();

        foreach ($categories as $c) {
            $trail->push($c['title'], route('category', ['category' => $c, 'categoryHru' => $c['hru']]));
        }
    }



    $trail->push(Str::limit($product->title_ae ?? '', 70), '');
});
