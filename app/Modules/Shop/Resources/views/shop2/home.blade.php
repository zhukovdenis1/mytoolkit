@extends('layouts.shop2')

@section('title', $article['title'] ?? $title)
@section('keywords', $article['keywords'] ?? $title)
@section('description',$article['description'] ?? $title)

@section('content')

    @if($popular?->isNotEmpty())
        {!! $article['content']["intro"] ?? '' !!}
        <x-shop::product-list :products="$popular" :more="false" />
    @endif

    @if(!$searchString)
        <div class="home-article">
            <h1>{{ $article['h1'] }}</h1>
            {!! $article['content']["main"] ?? '' !!}
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
