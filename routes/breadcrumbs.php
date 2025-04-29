<?php

use App\Modules\Shop\Models\ShopCategory;
use App\Modules\Shop\Models\ShopProduct;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

Breadcrumbs::for('home', function (BreadcrumbTrail $trail): void {
    $trail->push('Главная', route('home'));
});

Breadcrumbs::for('category', function (BreadcrumbTrail $trail, $categoryId): void {
    $query = ShopCategory::query()
        ->select('id_ae', 'title', 'hru', 'parents')
        ->where('id_ae', $categoryId)
        ->limit(1);
    $category = $query->first();

    $trail->parent('home');
    $trail->push($category->title, '');

});

Breadcrumbs::for('detail', function (BreadcrumbTrail $trail, $productId): void {
    $pd = ShopProduct::query()->find($productId, ['category_0','category_1','category_2','category_3', 'title']);
    $categories = [$pd->category_0,$pd->category_1,$pd->category_2,$pd->category_3];

    $trail->parent('home');

    $categories = ShopCategory::query()
        ->select('id_ae', 'title', 'hru')
        ->whereIn('id_ae', $categories)
        ->limit(4)
        ->get();

    foreach ($categories as $c) {
        $trail->push($c['title'], route('category', ['categoryId' => $c['id_ae'], 'categoryHru' => $c['hru']]));
    }

    $trail->push($pd->title ?? '', '');
});
