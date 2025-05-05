@extends('layouts.shop')

@section('content')
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Можно добавить кастомные настройки -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'primary': '#3b82f6',
                    }
                }
            }
        }
    </script>
    <div>
        <h1>Купоны</h1>

        {{-- Форма поиска --}}
        <form method="GET" action="">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Поиск купонов...">
            <button type="submit">
                Найти
            </button>

        </form>

        @if($coupons->isEmpty())
            <p class="text-gray-500">Купоны не найдены</p>
        @else
            <div class="flex md:mx-0 w-full flex-wrap md:mx-0 ">

            @foreach($coupons as $coupon)
                    <div class="px-[8px] flex-grow xs:block xs:w-[100%] xs:max-w-[100%] sm:block sm:w-[100%] sm:max-w-[100%] md:block md:w-[100%] md:max-w-[100%] lg:block lg:w-full lg:max-w-[100%] xl:block xl:w-[50%] xl:max-w-[50%] mb-5"><article class="relative flex flex-col flex-wrap w-full h-full p-3 bg-white border gap-0 xs:gap-[16px] xs:p-6 border-blueGray-200 rounded-2 xs:flex-row xs:flex-nowrap"><div class="flex flex-row items-end flex-shrink-0 xs:items-center gap-0 xs:gap-[16px] xs:flex-col sm:mr-4 sm:mb-0 md:mr-6 lg:mr-10"><div class="flex-grow min-w-[118px] xs:flex-grow-0"><img class="object-cover h-10 mt-0 xs:mt-4" src="https://cdn1.epn.bz/public/6fd958f8c6f66b229cdb6e05ab413366.png" alt=""></div><p class="flex-row items-center hidden px-2 py-2 text-center truncate border border-dashed xs:flex text-blueGray-600 h-[40px] border-blueGray-300 gap-[8px] xs:px-5 xs:w-full"><button type="button" class="bg-transparent"><svg aria-hidden="true" focusable="false" data-prefix="far" data-icon="copy" class="svg-inline--fa fa-copy fa-w-14 " role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M433.941 65.941l-51.882-51.882A48 48 0 0 0 348.118 0H176c-26.51 0-48 21.49-48 48v48H48c-26.51 0-48 21.49-48 48v320c0 26.51 21.49 48 48 48h224c26.51 0 48-21.49 48-48v-48h80c26.51 0 48-21.49 48-48V99.882a48 48 0 0 0-14.059-33.941zM266 464H54a6 6 0 0 1-6-6V150a6 6 0 0 1 6-6h74v224c0 26.51 21.49 48 48 48h96v42a6 6 0 0 1-6 6zm128-96H182a6 6 0 0 1-6-6V54a6 6 0 0 1 6-6h106v88c0 13.255 10.745 24 24 24h88v202a6 6 0 0 1-6 6zm6-256h-64V48h9.632c1.591 0 3.117.632 4.243 1.757l48.368 48.368a6 6 0 0 1 1.757 4.243V112z"></path></svg></button>

                                <div class="__react_component_tooltip tb70a415f-a40b-44ce-83a7-685a6d848311 place-top type-light epn-tooltip" id="CouponsPageCouponTooltip44250" data-id="tooltip" style="left: 574px; top: 156px;">
                                    ROCKRU004</div></p>

                            </div><div class="flex flex-col items-start w-full mt-4 xs:mt-0"><p class="mb-2 text-blueGray-500">01.05.2025 - 12.05.2025</p><h3 id="CouponsPageCouponDiscount44250" class="mb-2 font-medium text-blueGray-800 text-sub line-clamp-2">Промокод дает 13% на велосипеды ROCKBROS</h3><p id="CouponsPageCouponDescription44250" class="mb-4 text-blueGray-600 text-p2 pointer xl:min-h-20 relative"><p class="w-full break-words max-h-[3lh] overflow-hidden relative z-0">Скидка по промокоду 13% на велосипеды магазина ROCKBROS Official Store. Напоминаем, бренд имеет особое правило атрибуции. Это означает, что заказ с комиссией учитывается только при переходе и покупке по прямой ссылке, иначе комиссия по правилу AliExpress будет нулевой. Переходите по кнопке ниже на страницу акции и покупайте товары со скидками!</p><div class="Coupon_descriptionMask__13pUD"></div>

                                </p>
                                <div class="Coupon_couponBottom__bcjKW">
                                    <div class="flex mb-6">
                                    <div class="flex flex-row gap-[12px] xs:gap-[24px] w-full justify-between"><button id="CouponsPageCouponGetLinkButton44250" class="epn-button focusable w-full epn-button_medium epn-button_ghost" type="button">
                                            <svg aria-hidden="true" focusable="false" data-prefix="far" data-icon="spinner" class="svg-inline--fa fa-spinner fa-w-16 epn-button__loader animate-spin epn-button__loader_hidden epn-button__loader_spacing" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" hidden=""><path fill="currentColor" d="M296 48c0 22.091-17.909 40-40 40s-40-17.909-40-40 17.909-40 40-40 40 17.909 40 40zm-40 376c-22.091 0-40 17.909-40 40s17.909 40 40 40 40-17.909 40-40-17.909-40-40-40zm248-168c0-22.091-17.909-40-40-40s-40 17.909-40 40 17.909 40 40 40 40-17.909 40-40zm-416 0c0-22.091-17.909-40-40-40S8 233.909 8 256s17.909 40 40 40 40-17.909 40-40zm20.922-187.078c-22.091 0-40 17.909-40 40s17.909 40 40 40 40-17.909 40-40c0-22.092-17.909-40-40-40zm294.156 294.156c-22.091 0-40 17.909-40 40s17.909 40 40 40c22.092 0 40-17.909 40-40s-17.908-40-40-40zm-294.156 0c-22.091 0-40 17.909-40 40s17.909 40 40 40 40-17.909 40-40-17.909-40-40-40z"></path></svg>
                                            Получить ссылку</button>
                                        <a target="_blank" href="https://aliexpress.ru/store/1358398"><button class="epn-button focusable bg-red-100 min-w-[40px] p-[12px] rounded-full epn-button_medium" type="button"><svg aria-hidden="true" focusable="false" data-prefix="far" data-icon="spinner" class="svg-inline--fa fa-spinner fa-w-16 epn-button__loader animate-spin epn-button__loader_hidden epn-button__loader_spacing" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" hidden=""><path fill="currentColor" d="M296 48c0 22.091-17.909 40-40 40s-40-17.909-40-40 17.909-40 40-40 40 17.909 40 40zm-40 376c-22.091 0-40 17.909-40 40s17.909 40 40 40 40-17.909 40-40-17.909-40-40-40zm248-168c0-22.091-17.909-40-40-40s-40 17.909-40 40 17.909 40 40 40 40-17.909 40-40zm-416 0c0-22.091-17.909-40-40-40S8 233.909 8 256s17.909 40 40 40 40-17.909 40-40zm20.922-187.078c-22.091 0-40 17.909-40 40s17.909 40 40 40 40-17.909 40-40c0-22.092-17.909-40-40-40zm294.156 294.156c-22.091 0-40 17.909-40 40s17.909 40 40 40c22.092 0 40-17.909 40-40s-17.908-40-40-40zm-294.156 0c-22.091 0-40 17.909-40 40s17.909 40 40 40 40-17.909 40-40-17.909-40-40-40z"></path></svg><svg aria-hidden="true" focusable="false" data-prefix="far" data-icon="shopping-cart" class="svg-inline--fa fa-shopping-cart fa-w-18 text-brandColor" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="currentColor" d="M551.991 64H144.28l-8.726-44.608C133.35 8.128 123.478 0 112 0H12C5.373 0 0 5.373 0 12v24c0 6.627 5.373 12 12 12h80.24l69.594 355.701C150.796 415.201 144 430.802 144 448c0 35.346 28.654 64 64 64s64-28.654 64-64a63.681 63.681 0 0 0-8.583-32h145.167a63.681 63.681 0 0 0-8.583 32c0 35.346 28.654 64 64 64 35.346 0 64-28.654 64-64 0-18.136-7.556-34.496-19.676-46.142l1.035-4.757c3.254-14.96-8.142-29.101-23.452-29.101H203.76l-9.39-48h312.405c11.29 0 21.054-7.869 23.452-18.902l45.216-208C578.695 78.139 567.299 64 551.991 64zM208 472c-13.234 0-24-10.766-24-24s10.766-24 24-24 24 10.766 24 24-10.766 24-24 24zm256 0c-13.234 0-24-10.766-24-24s10.766-24 24-24 24 10.766 24 24-10.766 24-24 24zm23.438-200H184.98l-31.31-160h368.548l-34.78 160z"></path></svg></button></a></div></div>
                            </div></article></div>
            @endforeach
            </div>
        @endif

        @if($coupons->hasPages())
            {{ $coupons->withQueryString()->links() }}
        @endif
    </div>
@endsection
