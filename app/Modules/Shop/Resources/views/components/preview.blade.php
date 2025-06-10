@if(false && env('APP_DEBUG'))
<img src="/img/1.jpg" alt="Лягушки" />
@else
    @if ($product->photo)
        @if(strpos($product->photo[0], 'video') || strpos($product->photo[0], 'taobao'))
            <img src="{{$product->photo[1]}}_200x200.jpg" alt="{{$product->title ?? $product->title_ae}}" />
        @else
            <img src="{{$product->photo[0]}}_200x200.jpg" alt="{{$product->title ?? $product->title_ae}}" />
        @endif;
    @endif
@endif
