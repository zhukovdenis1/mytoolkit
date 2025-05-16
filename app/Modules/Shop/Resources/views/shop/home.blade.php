@extends('layouts.shop')

@section('title', $article['title'] ?? $title)
@section('keywords', $article['keywords'] ?? $title)
@section('description',$article['description'] ?? $title)

@section('content')
    @if(!$coupons->isEmpty() && !$searchString)
        <a class="h" href="{{route('coupons')}}">
            <h2>Действующие промокоды и купоны Алиэкспресс на {{$monthName}} {{date('Y')}}</h2> <span class="lnk">посмотреть все</span>
        </a>

        <div class="short-coupons-wrap">
            @foreach($coupons as $coupon)
                <x-shop::coupon-short :coupon="$coupon" />
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
@endsection
