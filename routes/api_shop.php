<?php

use App\Modules\Shop\Http\Controllers\Shared\ShopParseController;
use App\Modules\ShopArticle\Http\Controllers\Admin\ShopArticleController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\JwtAdminMiddleware;


Route::middleware([JwtAdminMiddleware::class])->group(function () {
    Route::prefix('admin')->group(function () {
        Route::prefix('shop')->group(function () {
            Route::prefix('articles')->group(function () {
                Route::get('/', [ShopArticleController::class, 'index']);
                Route::post('/', [ShopArticleController::class, 'store']);
                Route::get('{article}', [ShopArticleController::class, 'show']);
                Route::put('{article}', [ShopArticleController::class, 'update']);
                Route::put('{article}/edit-content', [ShopArticleController::class, 'updateContent']);
                Route::delete('{article}', [ShopArticleController::class, 'destroy']);
                Route::post('{article}/files', [ShopArticleController::class, 'storeFile']);
            });
        });
    });
});

Route::get('shop/get-product-for-parse', [ShopParseController::class, 'getProductForParse']);
Route::post('shop/set-parsed-product', [ShopParseController::class, 'setParsedProduct']);
Route::get('shop/get-coupon-for-parse', [ShopParseController::class, 'getCouponForParse']);
Route::post('shop/set-parsed-coupon', [ShopParseController::class, 'setParsedCoupon']);
