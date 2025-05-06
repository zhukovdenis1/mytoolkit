<?php

use App\Modules\Shop\Http\Controllers\Shared\ShopController;
use App\Modules\Shop\Http\Controllers\Shared\ShopCouponController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('', [ShopController::class, 'index'])->name('home');
Route::get('more', [ShopController::class, 'more'])->name('more');
Route::get('search', [ShopController::class, 'search'])->name('search');
Route::get('sitemap.xml', [ShopController::class, 'sitemap'])->name('sitemap');
Route::get('go', [ShopController::class, 'go'])->name('go');
Route::any('new-order', [ShopController::class, 'newOrder']);
Route::get('robots.txt', [ShopController::class, 'robots'])->name('robots');
Route::get('get-categories', [ShopController::class, 'getCategories'])->name('getCategories');
Route::get('c-{categoryId}/{categoryHru?}', [ShopController::class, 'category'])->name('category');
//->where(['categoryId' => '[0-9]+']);
Route::get('/coupons', [ShopCouponController::class, 'index'])->name('coupons');
Route::get('/coupons/{coupon}/{couponHru?}', [ShopCouponController::class, 'detail'])->name('coupon.detail');
Route::get('/p-{productId}/{productHru?}', [ShopController::class, 'detail'])->name('detail')
    ->where(['categoryId' => '[0-9]+']);
Route::get('/pa-{id_ae}', [ShopController::class, 'aedetail'])->name('aedetail')
    ->where(['id_ae' => '[0-9]+']);

Route::get('/ali/{productHru}', function (Request $request, $productHru) {
    return app()->make(ShopController::class)->oldDetail($request, $productHru, 'ali');
});
Route::get('/p/{productHru}/{lang?}', [ShopController::class, 'oldDetail']);
Route::get('/c/{categoryHru}', [ShopController::class, 'oldCategory']);


