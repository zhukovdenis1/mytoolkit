<?php

use App\Modules\Shop\Http\Controllers\Shared\ShopParseController;
use Illuminate\Support\Facades\Route;

Route::get('shop/get-product-for-parse', [ShopParseController::class, 'getProductForParse']);
Route::post('shop/set-parsed-product', [ShopParseController::class, 'setParsedProduct']);
Route::get('shop/get-coupon-for-parse', [ShopParseController::class, 'getCouponForParse']);
Route::post('shop/set-parsed-coupon', [ShopParseController::class, 'setParsedCoupon']);
Route::get('shop/get-product-for-reviews-parse', [ShopParseController::class, 'getProductForReviewsParse']);
Route::get('shop/get-product-for-reviews-tags-parse', [ShopParseController::class, 'getProductForReviewsTagsParse']);
Route::post('shop/set-parsed-reviews-tags', [ShopParseController::class, 'setParsedReviewsTags']);
Route::post('shop/set-parsed-reviews', [ShopParseController::class, 'setParsedReviews']);

