@if ($product->price_from && $product->price_to)
    {{ number_format($product->price_from, 0, ',', ' ') }} &mdash; {{ number_format($product->price_to, 0, ',', ' ') }} ₽
@else ($product->price)
    {{ number_format($product->price, 0, ',', ' ') }} ₽
@endif
