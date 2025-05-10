<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Modules\Shop\Models\ShopCoupon;
use App\Modules\ShopArticle\Models\ShopArticle;

class ShopCouponHelper
{
    public function __construct(private readonly HttpHelper $httpHelper)
    {}
    public function getRedirectUrl(int $couponId): ?string
    {
        $url = null;

        /** @var  ShopCoupon $coupon */
        $coupon = ShopCoupon::find($couponId);

        if (empty($coupon)) {
            return null;
        }

        if ($coupon->url) {
            return $coupon->url;
        }

        if ($coupon->pikabu_id && $pikabuUri = $coupon->info['url'] ?? '') {
            $url = 'https://promokod.pikabu.ru'. $pikabuUri;
        }

        return $url;
    }

}
