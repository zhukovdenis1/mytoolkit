@extends('layouts.shop')

@section('title', $article['title'] ?? $title)
@section('keywords', $article['keywords'] ?? $title)
@section('description',$article['description'] ?? $title)

@section('content')
    <div class="top-menu">
        <ul>
        @foreach ($epnCategories as $c)
            <li>
                <a href="{{route('epnCategory', ['categoryId' => $c['id'], 'categoryHru' => $c['uri']])}}">{{$c['name']}}</a>
            </li>
        @endforeach
        </ul>
    </div>
    @if(!$coupons->isEmpty() && !$searchString)
        <a class="h" href="{{route('coupons')}}">
            <h2>Действующие промокоды и купоны Алиэкспресс на {{$monthName}} {{date('Y')}}</h2> <span class="lnk">посмотреть все</span>
        </a>

        <div class="short-coupons-wrap">
            @foreach($coupons as $coupon)
                <x-shop::coupon :coupon="$coupon" />
            @endforeach
        </div>

        <p style="margin-bottom: 50px;">
            <a  href="{{route('coupons')}}">Посмотреть все скидки &raquo;</a>
        </p>

    @endif


    @if(!$articles->isEmpty() && !$searchString)
        <h3>Информационный блок:</h3>

        <div class="articles-wrap">
            @foreach($articles as $a)
                <a href="{{route('article.detail', ['article' => $a->id, 'articleHru' => $a->uri])}}">{{$a->h1}}</a>
            @endforeach
        </div>
    @endif

    <x-shop::product-list :products="$products" />
    @if(!$searchString)
        <div class="home-article">
            <h1>{{ $article['h1'] }}</h1>
            {!! $article['content'][0] ?? '' !!}
        </div>
    @endif
@endsection

@section('js')
    <script src="{{ asset('/shop/js/product_list_loading.js') }}"></script>
    <script type="text/javascript">
        ProductLoader.init({
            categoryId: 0,
            searchString: '{{ $searchString }}'
        });
    </script>

    <script src="{{ asset('/shop/js/coupon_list.js') }}"></script>
    <script type="text/javascript">
        CouponList.init({});
    </script>
@endsection
