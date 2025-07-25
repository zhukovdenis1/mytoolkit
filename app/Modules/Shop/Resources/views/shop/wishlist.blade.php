@extends('layouts.shop')

@section('title', 'Избранное / Недорогой интернет-магазин')
@section('description', 'Избранные товары товаров')
@section('keywords', 'Избранное')

@section('content')

    <h1>Избранное</h1>

    @if ($products->isEmpty())
        <p>{{__('Список желаний пуст')}}</p>
        <p>Перейти на <a href="/">главную</a></p>
    @else
        <p>Выбранные товары вы можете приобрести по указанной для кадой позициии ссылке</p>

            @foreach($products as $product)
            <div class="editor-product">
                <x-shop::product-go-link :product="$product" class="prod-item">
                    <span class="img-wrap">
                        <x-shop::preview :product="$product" />
                        <span class="bg"></span>
                    </span>
                                <span class="free">
                        <svg width="17" height="14" viewBox="0 0 17 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15.6851 8.121L13.579 6.905L12.933 4.48401C12.7 3.60901 11.907 3 11 3H9.60706L9.19995 1.48199C8.96595 0.607995 8.17394 0 7.26794 0H2C0.896 0 0 0.896 0 2V10C0 11.104 0.896 12 2 12H15C15.971 12 16.801 11.303 16.97 10.348C17.139 9.39199 16.5311 8.562 15.6851 8.121Z" fill="#00AD30"></path><path d="M4.5 14C5.88071 14 7 12.8807 7 11.5C7 10.1193 5.88071 9 4.5 9C3.11929 9 2 10.1193 2 11.5C2 12.8807 3.11929 14 4.5 14Z" fill="white"></path><path d="M4.5 13C5.32843 13 6 12.3284 6 11.5C6 10.6716 5.32843 10 4.5 10C3.67157 10 3 10.6716 3 11.5C3 12.3284 3.67157 13 4.5 13Z" fill="#00AD30"></path><path d="M12.5 14C13.8807 14 15 12.8807 15 11.5C15 10.1193 13.8807 9 12.5 9C11.1193 9 10 10.1193 10 11.5C10 12.8807 11.1193 14 12.5 14Z" fill="white"></path><path d="M12.5 13C13.3284 13 14 12.3284 14 11.5C14 10.6716 13.3284 10 12.5 10C11.6716 10 11 10.6716 11 11.5C11 12.3284 11.6716 13 12.5 13Z" fill="#00AD30"></path><path d="M12.7709 7.76099L11.9658 4.742C11.8488 4.304 11.4509 4 10.9989 4H8.83984L9.53088 6.582L12.7709 7.76099Z" fill="white"></path></svg>
                        <span title="Бесплатная доставка">бесплатно</span>
                    </span>
                    <x-shop::rating :rating="$product->rating" />
                </x-shop::product-go-link>
                <div>
                    <h3>{{ $product->title_source ?? $product?->title }}</h3>
                    <span class="price"><x-shop::price :product="$product" /></span>
                    <noindex>
                        <x-shop::product-go-link :product="$product" class="ae-link">
                            Посмотреть на AliExpress
                        </x-shop::product-go-link>
                    </noindex>
                </div>
            </div>
            @endforeach
    @endif

@endsection
