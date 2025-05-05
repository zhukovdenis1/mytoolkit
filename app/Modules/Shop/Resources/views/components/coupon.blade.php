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
        @if(($detail ?? false) === true)
            <p>{{ $coupon->description }}</p>
        @else
            <p>{{ Str::limit($coupon->description, 100) }} <a href="/coupons/{{$coupon->id}}/{{$coupon->uri}}" target="_blank" ">подбробнее</a> </p>
        @endif

    </div>
    <a href="{{ route('go', ['url' => $coupon->url]) }}" target="_blank" class="button">
        Использовать купон
    </a>
</div>
