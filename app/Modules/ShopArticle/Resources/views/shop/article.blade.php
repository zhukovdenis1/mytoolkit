@extends('layouts.shop')

@section('meta')
    <title>Статьи. {{ $coupon->title }}</title>
    <meta name="description" content="{{$coupon->description}}" />
    <meta name="keywords" content="{{ $coupon->title }}" />
@endsection


@section('content')
    <h1>{{$coupon->title}}</h1>
    <x-shop::coupon :coupon="$coupon" :detail="true" />
@endsection

