<?php

use App\Modules\Shop\Models\ShopCategory;
use App\Modules\Shop\Models\ShopCoupon;
use App\Modules\Shop\Models\ShopProduct;
use App\Modules\ShopArticle\Models\ShopArticle;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

Breadcrumbs::for('home', function (BreadcrumbTrail $trail): void {
    $trail->push('Главная', route('home'));
});

Breadcrumbs::for('oldDetail', function (BreadcrumbTrail $trail): void {
    $trail->push('Главная', route('home'));
    $trail->push('Страница товара', route('home'));
});

Breadcrumbs::for('coupons', function (BreadcrumbTrail $trail): void {
    $trail->push('Главная', route('home'));
    $trail->push('Купоны', route('coupons'));
});

Breadcrumbs::for('coupon.detail', function (BreadcrumbTrail $trail, ShopCoupon $coupon): void {
    $trail->push('Главная', route('home'));
    $trail->push('Купоны', route('coupons'));
    $trail->push($coupon->title, route('home'));
});

Breadcrumbs::for('articles', function (BreadcrumbTrail $trail): void {
    $trail->push('Главная', route('home'));
    $trail->push('Инфо', route('articles'));
});

Breadcrumbs::for('article.detail', function (BreadcrumbTrail $trail, ShopArticle $article): void {
    $trail->push('Главная', route('home'));
    $trail->push('Инфо', route('articles'));
    $trail->push($article->name ?? '', route('home'));

});

Breadcrumbs::for('category', function (BreadcrumbTrail $trail, $category): void {
//    $query = ShopCategory::query()
//        ->select('id_ae', 'title', 'hru', 'parents')
//        ->where('id_ae', $categoryId)
//        ->limit(1);
//    $category = $query->first();

    $trail->parent('home');
    $trail->push($category->title, '');

});

Breadcrumbs::for('detail', function (BreadcrumbTrail $trail, ShopProduct $product): void {
    //$pd = ShopProduct::query()->find($productId, ['category_0','category_1','category_2','category_3', 'title']);
    $categories = [$product->category_0, $product->category_1, $product->category_2, $product->category_3];

    $trail->parent('home');

    $categories = ShopCategory::query()
        ->select('id_ae', 'title', 'hru')
        ->whereIn('id_ae', $categories)
        ->limit(4)
        ->get();

    foreach ($categories as $c) {
        $trail->push($c['title'], route('category', ['category' => $c, 'categoryHru' => $c['hru']]));
    }

    $trail->push($pd->title ?? '', '');
});
