@extends('layouts.shop')

@section('title', $article['title'] ?: $article['h1'])
@section('keywords', $article['keywords'])
@section('description', $article['description'])

@section('content')
    <h1>{{ $article['h1'] }}</h1>

    {!! isset($article['content']['intro']) && $isIndexPage ? $article['content']['intro'] : '' !!}

    <form method="GET" action="">
        <span class="form-item"><input type="hidden" name="page" value="1" /></span>
        <span class="form-item">
                <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Поиск купонов..." />
        </span>
        <span class="form-item">
             <button type="submit">
                Найти
            </button>
        </span>


        <a class="coupon-type-lnk {{$type == 'code' ? 'active' : ''}}" href="{{route('coupons', ['page' => 1,'type'=>'code'])}}">Промокоды: <b>{{$counts['code']}}</b></a>
        <a class="coupon-type-lnk {{$type == 'discount_amount' ? 'active' : ''}}"href="{{route('coupons', ['page' => 1, 'type'=>'discount_amount'])}}">Скидка, руб: <b>{{$counts['discount_amount']}}</b></a>
        <a class="coupon-type-lnk {{$type == 'discount_percent' ? 'active' : ''}}" href="{{route('coupons', ['page' => 1, 'type'=>'discount_percent'])}}">Скидка, %: <b>{{$counts['discount_percent']}}</b></a>
        <a class="coupon-type-lnk {{$isIndexPage ? 'active' : ''}}" href="{{route('coupons')}}">Все активные скидки: <b>{{$counts['total']}}</b></a>
        <!--a href="#">Товары: 2</a-->
    </form>

    @if($coupons->isEmpty())
        <p>Купоны не найдены</p>
    @else
        <div class="short-coupons-wrap">
            @foreach($coupons as $coupon)
                <x-shop::coupon :coupon="$coupon" />
            @endforeach
        </div>

        @if($coupons->hasPages())
            {{ $coupons->withQueryString()->links() }}
        @endif

        {!! isset($article['content']['main']) && $isIndexPage ? $article['content']['main'] : '' !!}
    @endif
@endsection

@section('js')
    <script src="{{ asset('/shop/js/coupon_list.js') }}"></script>
    <script type="text/javascript">
        CouponList.init({});
    </script>
@endsection
