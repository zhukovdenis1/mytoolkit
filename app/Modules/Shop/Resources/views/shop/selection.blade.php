@extends('layouts.shop')

@section('title', $title . '/ Недорогой интернет магазин' ?? 'Недорогой интернет магазин')

@section('content')
    <h1>{{$title}}</h1>
    <x-shop::product-list :products="$products" />
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
