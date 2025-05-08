@extends('layouts.shop')

@section('meta')
    <title>{{ $article['title'] }}</title>
    <meta name="description" content="{{ $article['keywords'] }}" />
    <meta name="keywords" content="{{ $article['description'] }}" />
@endsection

@section('content')

    <h1>{{ $article['name'] }}</h1>

    {{-- Форма поиска --}}
    <form method="GET" action="">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Поиск купонов...">
            <button type="submit">
                Найти
            </button>

    </form>

    @if($coupons->isEmpty())
        <p>Купоны не найдены</p>
    @else
        {!! $article['introduction'] !!}

        <div class="coupons-wrap">
            @foreach($coupons as $coupon)
                    <x-shop::coupon :coupon="$coupon" />
            @endforeach
        </div>

        @if($coupons->hasPages())
            {{ $coupons->withQueryString()->links() }}
        @endif

        {!! $article['content'] !!}
    @endif



@endsection
