@extends('layouts.shop')

@section('title', $article['title'] ?: $article['h1'])
@section('keywords', $article['keywords'])
@section('description', $article['description'])

@section('content')
    <h1>{{ $article['h1'] }}</h1>

    {!! isset($article['content'][1]) && $isIndexPage ? $article['content'][1] : '' !!}

    <form method="GET" action="">
            <input type="hidden" name="page" value="1" />
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Поиск купонов...">
            <button type="submit">
                Найти
            </button>
        <a class="coupon-type-lnk {{$type == 'code' ? 'active' : ''}}" href="{{route('coupons', ['page' => 1,'type'=>'code'])}}">Промокоды: <b>{{$counts['code']}}</b></a>
        <a class="coupon-type-lnk {{$type == 'discount_amount' ? 'active' : ''}}"href="{{route('coupons', ['page' => 1, 'type'=>'discount_amount'])}}">Скидка, руб: <b>{{$counts['discount_amount']}}</b></a>
        <a class="coupon-type-lnk {{$type == 'discount_percent' ? 'active' : ''}}" href="{{route('coupons', ['page' => 1, 'type'=>'discount_percent'])}}">Скидка, %: <b>{{$counts['discount_percent']}}</b></a>
        <a class="coupon-type-lnk {{$isIndexPage ? 'active' : ''}}" href="{{route('coupons')}}">Все активные скидки: <b>{{$counts['total']}}</b></a>
        <!--a href="#">Товары: 2</a-->
    </form>

    @if($coupons->isEmpty())
        <p>Купоны не найдены</p>
    @else
                <div class="coupons-wrap">
            @foreach($coupons as $coupon)
                    <x-shop::coupon :coupon="$coupon" />
            @endforeach
        </div>

        @if($coupons->hasPages())
            {{ $coupons->withQueryString()->links() }}
        @endif

        {!! isset($article['content'][0]) && $isIndexPage ? $article['content'][0] : '' !!}
    @endif



@endsection
