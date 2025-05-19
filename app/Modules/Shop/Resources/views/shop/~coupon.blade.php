@extends('layouts.shop')

@section('title', $coupon->title . ' на Алиэкспресс')
@section('keywords', $coupon->title)
@section('description', $coupon->description)


@section('content')
    <h1>{{$coupon->title}}</h1>
    <x-shop::coupon :coupon="$coupon" :detail="true" />
@endsection
