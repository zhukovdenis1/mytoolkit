@extends('layouts.shop')

@section('title', $article->title ?? $article->h1)
@section('keywords', $article->keywords)
@section('description', $article->description)

@section('content')
    <div class="article">
        @if ($product)
            <div class="detail-menu">
                @if ($product->video)
                    <a href="{{route('detail', ['product' => $product, 'productHru' => $product->hru])}}#video">Видео</a>
                @endif
                @if ($product->characteristics)
                    <a href="{{route('detail', ['product' => $product, 'productHru' => $product->hru])}}#props">Характеристики</a>
                @endif
                @if ($product->reviews)
                    <a href="{{route('detail', ['product' => $product, 'productHru' => $product->hru])}}#reviews">Отзывы</a>
                @endif
                <span class="buy-button2 _aeLink" data-id="{{$product->id}}" data-id_ae="{{$product->id_ae}}">Купить на AliExpress</span>
                <a href="{{ route('coupons') }}" target="_blank" class="coupon-button">Купоны&nbsp;на&nbsp;скидку</a>
            </div>
        @endif
        <h1>{{$article->h1}}</h1>
        {!! $article->text !!}
        @if ($product)
            <p>
                <a href="{{route('detail', ['product' => $product, 'productHru' => $product->hru])}}">Читать подробнее о товаре (Фото/видео, Отзывы, Характеристики ...) &raquo;</a>
            </p>
        @endif
    </div>
@endsection

@section('js')
    <script type="text/javascript" src="/shop/js/buy_link.js"></script>
@endsection

