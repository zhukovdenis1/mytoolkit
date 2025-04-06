@if ($product->title && strpos(trim($product->title), ' '))
    {{ trim($product->title) }}
@else
    {{$product->title_ae}}
@endif
