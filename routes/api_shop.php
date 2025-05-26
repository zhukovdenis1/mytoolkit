<?php

use App\Modules\Shop\Http\Controllers\Shared\ShopController;
use App\Modules\Shop\Http\Controllers\Shared\ShopParseController;
use Illuminate\Support\Facades\Route;

Route::get('shop/get-product-for-parse', [ShopParseController::class, 'getProductForParse']);
Route::post('shop/set-parsed-product', [ShopParseController::class, 'setParsedProduct']);
Route::get('shop/get-coupon-for-parse', [ShopParseController::class, 'getCouponForParse']);
Route::post('shop/set-parsed-coupon', [ShopParseController::class, 'setParsedCoupon']);

