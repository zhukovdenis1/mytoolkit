@extends('layouts.shop')

@section('title', $article['title'] ?? $category['name'] . '/ Недорогой интернет магазин')
@section('keywords', $article['keywords'] ?? $category['name'])
@section('description',$article['description'] ?? $category['name'])

@section('content')
    <h1>{{$article['h1'] ?? $category['name']}}</h1>
    <x-shop::product-list :products="$products" />
    @if(!$searchString && $article)
        <div class="shop-article">
            {!! $article['content']['main'] ?? '' !!}
        </div>
    @endif
@endsection

@section('js')
    <script src="{{ asset('/shop/js/product_list_loading.js') }}"></script>
    <script type="text/javascript">
        ProductLoader.init({
            epnCategoryId: {{ $category['id'] }},
            searchString: '{{ $searchString }}'
        });
    </script>
@endsection
