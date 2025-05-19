@extends('layouts.shop')

@section('title', $title ?? 'Недорогой интернет магазин')

@section('content')
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
