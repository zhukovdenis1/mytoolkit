<a class="short-coupon" href="#">
     <span class="discount">
        @if($coupon->discount_amount)
             -{{$coupon->discount_amount}}&nbsp;руб.
         @else
             {{$coupon->discount_percent ? '-'.$coupon->discount_percent: ''}}%
         @endif
    </span>
    <span class="info">
        <span class="title">{{ $coupon->title }}</span>
        <span class="date">
            Активен до {{ $coupon->date_to->format('d.m.Y') }}
        </span>
    </span>
</a>
<div class="details">
    <div class="coupon">
        <div>
            <img src="/shop/img/alicoupon.png" alt="Aliexpress coupon">
            <div class="date">
                {{ $coupon->date_from->format('d.m.Y') }} &mdash; {{ $coupon->date_to->format('d.m.Y') }}
            </div>
            @if($coupon->code)
                <div class="code">{{ $coupon->code }}</div>
            @endif
        </div>
        <div class="info">
            <h2>{{ $coupon->title }}</h2>
            <p>{{ $coupon->description }}</p>
        </div>
        <a href="{{ route('go', ['coupon_id' => $coupon->id]) }}" target="_blank" class="button">
            Использовать купон
        </a>
    </div>
</div>
