<a class="short-coupon" href="/coupons/{{$coupon->id}}/{{$coupon->uri}}>">
    <span class="discount">
        @if($coupon->discount_amount)
            -{{$coupon->discount_amount}}&nbsp;руб.
        @else
            -{{$coupon->discount_percent}}%
        @endif
    </span>
    <span class="info">
        <span class="title">{{ $coupon->title }}</span>
        <span class="date">
            Активен до {{ $coupon->date_to->format('d.m.Y') }}
        </span>
    </span>
</a>
