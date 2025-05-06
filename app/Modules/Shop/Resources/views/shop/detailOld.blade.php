@extends('layouts.shop')

@section('meta')
    <title>{{$title}}</title>
    <meta name="description" content="{{$description}}" />
    <meta name="keywords" content="{{ $keywords }}" />
@endsection

@section('content')
    <style>
        .tr-notice {
            display: none;
        }
    </style>
    <div class="detail">
        <div class="detail-menu">
            <a href="{{ route('go', ['search' => $h1]) }}" target="_blank" class="buy-button2">Купить на AliExpress</a>
            <a href="{{ route('coupons') }}" target="_blank" class="coupon-button">Купоны</a>
        </div>
        <div class="detail-inner">
            {!! $content !!}
        </div>
    </div>




@endsection


