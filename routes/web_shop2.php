<?php

use App\Modules\Shop\Http\Controllers\Shared\Shop2Controller;
use App\Modules\Shop\Http\Controllers\Shared\ShopCouponController;
use App\Modules\ShopArticle\Http\Controllers\Shared\ShopArticleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



    Route::get('', [Shop2Controller::class, 'index'])->name('home');
    //Route::get('more', [Shop2Controller::class, 'more'])->name('more');//->withoutMiddleware(['shop_visits']);
    Route::get('product_more', [Shop2Controller::class, 'productMore'])->name('reviewsMore');//->withoutMiddleware(['shop_visits']);
    Route::get('search', [Shop2Controller::class, 'search'])->name('search');
    Route::get('sitemap.xml', [Shop2Controller::class, 'sitemap'])->name('sitemap');
    Route::get('go', [Shop2Controller::class, 'go'])->name('go');
    Route::get('robots.txt', [Shop2Controller::class, 'robots'])->name('robots');
    //Route::get('shop/visit', [Shop2Controller::class, 'visit']);
    Route::get('add-to-cart', [Shop2Controller::class, 'addToCart'])->name('addToCart');
    Route::get('cart', [Shop2Controller::class, 'cart'])->name('cart');
    Route::get('add-to-wishlist', [Shop2Controller::class, 'addToWishlist'])->name('addToWishlist');
    Route::get('wishlist', [Shop2Controller::class, 'wishlist'])->name('wishlist');


    Route::get('coupons', [Shop2Controller::class, 'goHome'])->name('coupons');
    //Route::get('coupons/{coupon}/{couponHru?}', [ShopCouponController::class, 'detail'])->name('coupon.detail');
    //Route::get('a', [ShopArticleController::class, 'index'])->name('articles');
    //Route::get('a-{article}/{articleHru?}', [Shop2Controller::class, 'detail'])->name('article.detail');
    Route::get('r-{review}/{reviewHru?}', [Shop2Controller::class, 'detail'])->name('review.detail');
    Route::get('r-{product}/{productHru?}', [Shop2Controller::class, 'goHome'])->name('detail');



