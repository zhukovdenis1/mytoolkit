<?php

use App\Modules\Shop\Http\Controllers\Shared\ShopController;
use App\Modules\Shop\Http\Controllers\Shared\ShopCouponController;
use App\Modules\ShopArticle\Http\Controllers\Shared\ShopArticleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['shop_visits'])->group(function () {

    Route::get('', [ShopController::class, 'index'])->name('home');
    Route::get('more', [ShopController::class, 'more'])->name('more');//->withoutMiddleware(['shop_visits']);
    Route::get('search', [ShopController::class, 'search'])->name('search');
    Route::get('sitemap.xml', [ShopController::class, 'sitemap'])->name('sitemap');
    Route::get('go', [ShopController::class, 'go'])->name('go');
    Route::any('new-order', [ShopController::class, 'newOrder']);
    Route::get('robots.txt', [ShopController::class, 'robots'])->name('robots');
    Route::get('get-categories', [ShopController::class, 'getCategories'])->name('getCategories');
    Route::get('shop/visit', [ShopController::class, 'visit']);


    Route::get('coupons', [ShopCouponController::class, 'index'])->name('coupons');
//Route::get('coupons/{coupon}/{couponHru?}', [ShopCouponController::class, 'detail'])->name('coupon.detail');
    Route::get('a', [ShopArticleController::class, 'index'])->name('articles');
    Route::get('a-{article}/{articleHru?}', [ShopArticleController::class, 'detail'])->name('article.detail');
    Route::get('c-{category}/{categoryHru?}', [ShopController::class, 'category'])->name('category');
    Route::get('ce-{categoryId}/{categoryHru?}', [ShopController::class, 'epnCategory'])->name('epnCategory');
    Route::get('p-{product}/{productHru?}', [ShopController::class, 'detail'])->name('detail');
    Route::get('/s/{selectionName}/', [ShopController::class, 'selection'])->name('selection');
    Route::get('pa-{id_ae}', [ShopController::class, 'aedetail'])->name('aedetail')
        ->where(['id_ae' => '[0-9]+']);



//old site pages
    Route::get('/c/flashlights-lasers-999/flashlight-parts-and-tools-917_143', function (Illuminate\Http\Request $request) {
        return redirect('/s/flashlights', 301);
    })->withoutMiddleware(['shop_visits']);
    Route::get('/c/flashlights-lasers-999/flashlight-parts-and-tools-917/glass-954_592', function (Illuminate\Http\Request $request) {
        return redirect('/s/flashlights', 301);
    })->withoutMiddleware(['shop_visits']);
    Route::get('/ali/{productHru}', function (Request $request, $productHru) {
        return app()->make(ShopController::class)->oldDetail($request, $productHru, 'ali');
    })->name('oldDetail');
    Route::get('/p/{productHru}/{lang?}', [ShopController::class, 'oldDetail'])->name('oldDetailLang');
    Route::get('/c/{categoryHru}/{categoryHru2?}/{categoryHru3?}', [ShopController::class, 'oldCategory'])->name('oldCategory');
});

