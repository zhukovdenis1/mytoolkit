@extends('layouts.shop')

@section('meta')
    <title>Купоны Алиэкспресс {{ date('Y') }}</title>
    <meta name="description" content="Купоны Алиэкспресс {{ date('Y') }}" />
    <meta name="keywords" content="Купоны Алиэкспресс {{ date('Y') }}" />
@endsection

@section('content')

    <h1>Купоны Алиэкспресс {{ date('Y') }}</h1>

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
        <div class="coupons-wrap">
        @foreach($coupons as $coupon)
                <x-shop::coupon :coupon="$coupon" />
        @endforeach
        </div>
    @endif

    @if($coupons->hasPages())
        {{ $coupons->withQueryString()->links() }}
    @endif

@endsection
