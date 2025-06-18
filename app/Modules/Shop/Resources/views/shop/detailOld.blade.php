@extends('layouts.shop')

@section('title', $title)
@section('keywords', $keywords)
@section('description', $description)

@section('content')
    <style>
        .tr-notice {
            display: none;
        }
        .pimgsubbox table {
            width: auto!important;
        }
        .pimgsubbox table td {
            width: auto!important;
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


