@extends($baseLayout)

@section('title')
    @if ($reviewArticle)
        {{$reviewArticle->title ?? $reviewArticle->h1}}
    @else
        <x-shop::product_title :product="$p" />— купить онлайн
    @endif
@endsection
@section('keywords')
    @if ($reviewArticle)
        {{$reviewArticle->keywords}}
    @else
        {{$p->title_ae . ',' . ' Недорогой интернет-магазин' . $p->title_source}}
    @endif
@endsection
@section('description')
    @if ($reviewArticle)
        {{$reviewArticle->description}}
    @else
        {{$p->title_ae . '. Купить недорого. '. $p->title_source}}
    @endif
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
    <!--noindex>
        <x-shop::product-go-link :product="$p" class="buy-button2">
            Купить на AliExpress
        </x-shop::product-go-link>
    </noindex-->
        <span class="buy-button2 _aeLink" data-id="{{$p->id}}" data-id_ae="{{$p->id_ae}}">Купить на AliExpress</span>
    <a href="{{ route('coupons') }}" target="_blank" class="coupon-button">Купоны&nbsp;на&nbsp;скидку</a>
    @if ($reviewArticle)
            <a href="#review">Обзор на товар</a>
    @endif
</div>

<div class="detail-inner">

    <div id="vkGroup" class="vk-group-widget"></div>
    @if ($reviewArticle)
        <h1>{{$reviewArticle->h1}}</h1>
    @else
        <h1>{{$p->title ?: $p->title_ae}}</h1>
    @endif


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
            <div style="float: right">
                <span class="wishlist _wishlist" data-id="{{$p->id}}">В избранное</span>
            </div>
        </div>

        <span class="cart-link _cart" data-id="{{$p->id}}"><strong>В корзину</strong> <!--span>также&nbsp;будет&nbsp;ссылка&nbsp;на&nbsp;Алиэкспресс</span--></span>
            <span data-id="{{$p->id}}" data-id_ae="{{$p->id_ae}}" class="ae-link _aeLink">
                <strong>Купить на AliExpress</strong> <span>(цена&nbsp;могла&nbsp;измениться)</span>
            </span>
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


    @if ($reviewArticle)
        <div id="review">
            <h2>Обзор на товар</h2>
            {!! $reviewArticle->text !!}
        </div>
    @endif

    <div class="clearing"></div>

    @if ($p->characteristics)
    <div class="clearing"></div>
    <div class="props" id="props">
        <h2>Характеристики</h2>
        @if ($reviewArticle)
            <div class="loading" id="characteristicsLoading"></div>
        @else
            {!! $p->characteristics !!}
        @endif
    </div>
    @endif

    @if ($reviewArticle)
        <div class="clearing"></div>
        <div class="reviews" id="reviews">
            <h2>
                Отзывы
            </h2>
            <ul class="reviews" id="reviewList">
            </ul>
        </div>
        <div class="loading" id="reviewLoading"></div>
    @else
        @if ($p->reviews)
            <div class="clearing"></div>
            <div class="reviews" id="reviews">
                <h2>
                    Отзывы
                </h2>
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
                                    @if ($reviewArticle)
                                        <span class="text">{{ Str::limit($r['root']['text'], 1000) }}</span>
                                    @else
                                        <span class="text">{{ $r['root']['text'] }}</span>
                                    @endif
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
                                    @if ($reviewArticle)
                                        <span class="text">{{ Str::limit($r['text'], 200) }} ...</span>
                                    @else
                                        <span class="text">{{ $r['text'] }}</span>
                                    @endif

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
    @endif

    @if ($p->description && $reviewArticle)
        <div class="clearing"></div>
        <div class="description" id="description">
            <h2>Описание</h2>
            <div class="loading" id="descriptionLoading"></div>
        </div>
    @endif

    @if ($p->description && !$reviewArticle)
    <div class="clearing"></div>
    <div class="description" id="description">
        <h2>Описание</h2>
        {!! $p->description !!}
    </div>
    @endif


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
    @if ($reviewArticle)

        <script src="{{ asset('/shop/js/product_more_loading.js') }}"></script>
        <script type="text/javascript">
            ProductMoreInfoLoader.init({
                product_id: {{ $p->id }}
            });
        </script>
    @endif
    <script type="text/javascript">
        $('a.prodImg').colorbox({rel:'gal', maxWidth:'100%', maxHeight: '100%'});
        $('a.reviewImg').colorbox({rel:'review-img', maxWidth:'100%', maxHeight: '100%'});
        // $(document).on('click', 'a.reviewImg', function(e) {
        //     e.preventDefault();
        //     $.colorbox({rel:'review-img', maxWidth:'100%', maxHeight: '100%', href: $(this).attr('href')});
        // });
        $('._cart').click(function() {
            let productId = $(this).attr('data-id');
            $.ajax({
                dataType: 'json',
                type: "GET",
                url: "{{route('addToCart')}}",
                data: {'id': productId},
                cache: false,
                async: true,
                success: function(json){
                    $('._cartCounter').text(json.amount);
                }
            });

            $.colorbox({html:'<div style="margin:0 10px;text-align: center">' +
                "<h2 style='margin:0;'>Товар добавлен в корзину </h2>" +
                '<p><a href="/cart" style="font-size: 1.2em">Перейти в корзину</a></p></div>'
            });
        });

        $('._wishlist').click(function() {
            let productId = $(this).attr('data-id');
            $.ajax({
                dataType: 'json',
                type: "GET",
                url: "{{route('addToWishlist')}}",
                data: {'id': productId},
                cache: false,
                async: true,
            });

            $.colorbox({html:'<div style="margin:0 10px;text-align: center">' +
                    "<h2 style='margin:0;'>Товар добавлен в избранное </h2>" +
                    '<p><a href="/wishlist" style="font-size: 1.2em">Перейти в избранное</a></p></div>'
            });
        });
    </script>
    <script type="text/javascript" src="/shop/js/buy_link.js"></script>
@endsection

