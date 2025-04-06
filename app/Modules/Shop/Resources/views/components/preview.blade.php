@if(false && env('APP_DEBUG'))
<img src="/img/1.jpg" alt="Лягушки" />
@else
<img src="{{$product->photo[0]}}_200x200.jpg" alt="{{$product->title}}" />
@endif
