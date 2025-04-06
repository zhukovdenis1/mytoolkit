@if ($product->price_from && $product->price_to)
    {{$product->price_from}} &mdash; {{$product->price_to}} руб
@else
    {{$product->price}} руб
@endif
