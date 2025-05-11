@extends('layouts.shop')

@section('meta')
    <title><x-shop::product_title :product="$p" />  — купить онлайн / Недорогой интернет-магазин</title>
    <meta name="description" content="Недорогой интернет-магазин DealExtreme. {{ $p->title ?: $p->title_ae }}. Посредник DealExtreme по России и СНГ. DealExtreme - китайский интернет-магазин дешевых товаров. Основным преимуществом DealExtreme помимо низких цен является бесплатная доставка по всему миру." />
    <meta name="keywords" content="{{ $p->title ?: $p->title_ae }}. Недорогой интернет-магазин DealExtreme, Dealextreme на русском, dialextrim, dealextream, дешевый интернет-магазин, недорогой интернет-магазин, интернет магазин китайских товаров с бесплатной доставкой, китайские интернет магазины на русском языке, dealextreme аналог Россия СНГ, китайский интернет магазин на русском" />
@endsection

@section('content')
<div class="detail">
<!--$images = $this->getImages($p);-->
<div class="detail-menu">
    @if ($images)
        <a href="#photo">Фото</a>
    @endif
        @if ($p->video)
            <a href="#video">Видео</a>
        @endif
    @if ($p->description)
        <a href="#description">Описание</a>
    @endif
    @if ($p->characteristics)
        <a href="#props">Характеристики</a>
    @endif
    @if ($p->reviews)
        <a href="#reviews">Отзывы</a>
    @endif
    <noindex>
        <x-shop::product-go-link :product="$p" class="buy-button2">
            Купить на AliExpress
        </x-shop::product-go-link>
    </noindex>
    <a href="{{ route('coupons') }}" target="_blank" class="coupon-button">Купоны</a>
</div>

<div class="detail-inner">

    <div id="vkGroup" class="vk-group-widget"></div>

    <h1>{{$p->title ?: $p->title_ae}}</h1>

    <div class="img-wrap">
        @if ($images)
        <a class="prodImg" rel="gal" target="_blank" href="{{ $images[0] }}"><img src="{{ $images[0] }}_250x250.jpg" title="{{ $p->title }}" alt="{{ $p->title }}" /></a>
        @endif
    </div>

    <div class="prod-info">


        @if ($p->title)
        <h2 class="title">
            {{ $p->title_ae }}
        </h2>
        @endif
        @if ($p->title_source)
            <h2 class="title">
                {{ $p->title_source }}
            </h2>
        @endif
        <x-shop::rating :rating="$p->rating" />

        <div class="price">
            <p>
                <!--Цена могла измениться. Актуальную цену смотрите на AliExpress-->
            </p>
            Цена: <strong><x-shop::price :product="$p" /></strong>
        </div>

        <noindex>
            <x-shop::product-go-link :product="$p" class="ae-link">
                Купить на AliExpress
            </x-shop::product-go-link>
        </noindex>
    </div>

    <div class="images" id="photo">
        @if ($images)
        <a class="prodImg firstImg" rel="gal" href="{{ $images[0] }}" target="_blank"><img src="{{ $images[0] }}_200x200.jpg" alt="{{ $p->title }}#0" /></a>
        @endif
        @for ($i = 1; $i < count($images); $i++)
            <a class="prodImg" rel="gal" href="{{ $images[$i] }}" target="_blank"><img src="{{ $images[$i] }}_200x200.jpg" alt="{{ $p->title }}#<?=$i?>" /></a>
        @endfor
        {!! $vkAttachment !!}
    </div>

    @if ($p->video)
        <div class="video" id="video">
        @foreach ($p->video as $v)
            <video controls >
                <source src="{{ $v }}" type="video/webm" />
                <source src="{{ $v }}" type="video/mp4" />
                <a href="{{ $v }}" target="_blank">MP4</a>
            </video>
        @endforeach
        </div>
    @endif

    <div class="clearing"></div>

    @if ($p->characteristics)
    <div class="clearing"></div>
    <div class="props" id="props">
        <h2>Характеристики</h2>
        {!! $p->characteristics !!}
    </div>
    @endif

    @if ($p->description)
    <div class="clearing"></div>
    <div class="description" id="description">
        <h2>Описание</h2>
        {!! $p->description !!}
    </div>
    @endif

    @if ($p->reviews)
    <div class="clearing"></div>
    <div class="reviews" id="reviews">
        <h2>Отзывы</h2>
        <ul class="reviews">
        @foreach ($p->reviews as $r)
                @if (isset($r['reviewer']))
                    <li>
                        <div class="profile">
                            <div class="profile-photo">
                                <img class="profile" src="{{ $r['reviewer']['avatar'] }}" />
                                @isset($r['reviewer']['countryFlag'])
                                    <img class="country" src="{{ $r['reviewer']['countryFlag'] }}" />
                                @endisset
                            </div>
                            <div>
                                @if (isset($r['root']['grade']) && isset($r['reviewer']['name']) && isset($r['root']['date']))
                                    <x-shop::rating :rating="$r['root']['grade']*10" />
                                    <span class="user-name">{{ $r['reviewer']['name'] }}</span>
                                    <span class="date">{{ $r['root']['date'] }}</span>
                                @endif
                            </div>

                        </div>
                        @isset($r['root']['text'])
                            <span class="text">{{ $r['root']['text'] }}</span>
                        @endisset

                        <div class="img-list">
                            @isset($r['root']['images'])
                                @foreach ($r['root']['images'] as $img)
                                    <a class="reviewImg" rel="review-img" target="_blank" href="{{ $img['url'] }}"><img class="img" src="{{ $img['url'] }}_100x100.jpg" /></a>
                                @endforeach
                            @endisset
                        </div>
                    </li>
                @elseif ($r)
                <li>
                    <div class="profile">
                        <div class="profile-photo">
                            <img class="profile" src="{{ $r['user']['profileImageUrl'] ?? '' }}" />
                            @isset($r['user']['countryImageUrl'])
                            <img class="country" src="{{ $r['user']['countryImageUrl'] }}" />
                            @endisset
                        </div>
                        <div>
                            @if (isset($r['grade']) && isset($r['user']['name']) && isset($r['date']))
                            <x-shop::rating :rating="$r['grade']*10" />
                            <span class="user-name">{{ $r['user']['name'] }}</span>
                            <span class="date">{{ $r['date'] }}</span>
                            @endif
                        </div>

                    </div>
                    @isset($r['text'])
                    <span class="text">{{ $r['text'] }}</span>
                    @endisset

                    <div class="img-list">
                    @isset($r['images'])
                        @foreach ($r['images'] as $img)
                            <a class="reviewImg" rel="review-img" target="_blank" href="{{ $img['url'] }}"><img class="img" src="{{ $img['url'] }}_100x100.jpg" /></a>
                        @endforeach
                    @endisset
                    </div>
                </li>
                @endif
        @endforeach
        </ul>
    </div>
    @endif

    <div class="clearing"></div>


    <!--div class="packaging">
        <h2>Упаковка</h2>
        {!! $p->packaging !!}
    </div-->

    <div class="clearing"></div>

    <div class="recommends">
        @if (count($recommends))
        <h2>Рекомендуем посмотреть:</h2>
        <ul class="prod-list">
             @foreach ($recommends as $r)
            <li>
                <a href="{{ route('detail', ['product' => $r['id']]) }}">
                    <span class="img-wrap"><x-shop::preview :product="(object) $r" /></span>
                    <x-shop::rating :rating="$r['rating']" />
                    <span class="title">{{ $r['title'] }}</span>
                    <span class="price"><x-shop::price :product="(object) $r" /></span>
                </a>
            </li>
            @endforeach
        </ul>
        @endif
    </div>

    <div class="clearing"></div>
</div>
</div>

@endsection

@section('js')
    <script type="text/javascript">
        $('a.prodImg').colorbox({rel:'gal', maxWidth:'100%', maxHeight: '100%'});
        $('a.reviewImg').colorbox({rel:'review-img', maxWidth:'100%', maxHeight: '100%'});
    </script>
@endsection

