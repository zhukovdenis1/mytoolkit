@extends('layouts.shop')

@section('title', $category->title . '/ Недорогой интернет магазин' ?? 'Недорогой интернет магазин')

@section('content')
    <x-shop::product-list :products="$products" />
@endsection

@section('js')
    <script src="{{ asset('/shop/js/product_list_loading.js') }}"></script>
    <script type="text/javascript">
        ProductLoader.init({
            categoryId: {{ $category->id_ae }},
            searchString: '{{ $searchString }}'
        });
    </script>
@endsection
