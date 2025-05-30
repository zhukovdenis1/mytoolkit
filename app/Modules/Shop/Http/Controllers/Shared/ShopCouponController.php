<?php

declare(strict_types=1);

namespace App\Modules\Shop\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Modules\Shop\Models\ShopCoupon;
use App\Modules\Shop\Services\ShopCouponService;
use Illuminate\Http\Request;

class ShopCouponController extends Controller
{
    public function __construct(private readonly ShopCouponService $service) {}
    public function index(Request $request)
    {
        $validated = $request->validate([
            'search'      => ['nullable', 'string', 'min:1', 'max:100'],
            'type'        => ['nullable', 'string', 'min:1', 'max:20'],
            'page'        => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);


        $coupons = $this->service->findPaginated($validated);

        return view('Shop::shop.coupons', [
            'article' => $this->service->getArticleData(),
            'coupons' => $coupons,
            'counts' => $this->service->getCounts(),
            'type' => $validated['type'] ?? null,
            'isIndexPage' => (
                empty($validated['search'])
                && empty($validated['type'])
                && (empty($validated['page']) || $validated['page'] == 1)
            )
        ]);
    }

    public function detail(ShopCoupon $coupon, string $couponHru='')
    {
        if ($couponHru != $coupon->uri) {
            return redirect()->route('coupon.detail', ['coupon' => $coupon, 'couponHru' => $coupon->uri], 301);
        }

        return view('Shop::shop.coupon', [
            'coupon' => $coupon,
        ]);
    }
}
