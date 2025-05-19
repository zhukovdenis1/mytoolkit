<!doctype html>
<html lang="en">
<head>
    @inject('seo', 'App\Services\ShopSeoService')
    {!! $seo->getNoIndexTag(view()->yieldContent('noindex'), request()) !!}
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="/shop/img/favicon.png" type="image/png">
    <title>{{$seo->getTitle(view()->yieldContent('title'), request())}}</title>
    <meta name="description" content="{{$seo->getDescription(view()->yieldContent('description'), request())}}">
    <meta name="keywords" content="{{$seo->getKeywords(view()->yieldContent('keywords'), request())}}">


    <link rel="stylesheet" href="/shop/css/main.css?{{date('H')}}" />
    <link rel="stylesheet" href="/shop/css/colorbox.css" />
    @stack('css')

    <meta name="yandex-verification" content="5d0ecdba6a014fcc" />
    <meta name="google-site-verification" content="Eougu2REuO6g1AZkpyBbgV_VdahDZ_ffBBsXtiikB8c" />
</head>
<body>
<div class="wrapper">
    <div class="center-wrap">
        <header class="">
            <div class="left">
                <a href="/" class="logo"></a>
            </div>
            <div class="lef2">
                <div class="burger-menu" id="burgerMenuButton">все<br />категории</div>
                <nav class="main-menu" style="display: none" id="burgerMenu">
                </nav>
            </div>
            <div class="middle">
                <div class="searchForm">
                    <form action="/" id="searchForm">
                        <input type="text" name="search" placeholder="Я ищу..." value="">
                        <span class="sbutton" id="searchButton"></span>
                    </form>
                </div>
            </div>
            <div class="right">
                <noindex>
                <div class="account"><a href="{{ route('go', ['search' => '{login}']) }}" rel="nofollow" target="_blank">Войти</a><br /><a href="{{ route('go', ['search' => '{login}']) }}" rel="nofollow" target="_blank">Регистрация</a></div>
                    <a class="wish" href="{{ route('go', ['search' => '{wishlist}']) }}" target="_blank" rel="nofollow">Мои<br />желания</a>
                    <a class="cart" href="{{ route('go', ['search' => '{basket}']) }}" target="_blank" rel="nofollow"><span class="counter">0</span>Корзина</a>
                </noindex>
            </div>
        </header>
        <!--nav class="center-menu">
            <a href="/coupons">Купоны</a>
        </nav-->
        <main>
            <article>
                @if(!Route::is('home')) {{ Breadcrumbs::render() }} @else <ol class="brcr"></ol> @endif
                @yield('content')
            </article>
            <!--aside>Asided</aside-->

        </main>
        <footer>Подборка товаров с <a href="{{route('articles')}}">Алиэкспресс</a> &copy;{{ date('Y') }}</footer>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="/shop/js/jquery.colorbox-min.js"></script>

    <script>
        $('#searchButton').click(function() {
            $('#searchForm').submit();
        });

        $('#burgerMenuButton').click(function() {
            $(this).toggleClass('active');
            $('#burgerMenu').empty().append('<div class="loading" style="height: 30px; margin: 20px 0;"></div>').toggle();
            $.ajax({
                dataType: 'json',
                type: "get",
                url: "/get-categories",
                data: {var: 'value'},
                cache: false,
                success: function(json){
                    let menu = '<ul>';
                    $.each(json, function(index, val){
                        menu += '<li><a href="/c-'+val.id_ae+'/'+val.hru+'">'+ val.title + '</a></li>';
                    });
                    menu += '</ul>';
                    $('#burgerMenu').empty().append(menu);
                    $('#burgerMenu').height($(window).height()-$('header').height());
                }
            });

        });

        //$('._modal').colorbox();

        $('._modal_inline').colorbox({
            inline:true,
            width: $('.center-wrap').width(),
            height: '90%'
        });
    </script>

    @yield('js')

    @if(!env('APP_DEBUG'))
    <!-- Yandex.Metrika counter -->
    <script type="text/javascript" >
        (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
            m[i].l=1*new Date();
            for (var j = 0; j < document.scripts.length; j++) {if (document.scripts[j].src === r) { return; }}
            k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
        (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

        ym(32676105, "init", {
            clickmap:true,
            trackLinks:true,
            accurateTrackBounce:true,
            webvisor:true
        });
    </script>
    <noscript><div><img src="https://mc.yandex.ru/watch/32676105" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
    @endif
    <!-- /Yandex.Metrika counter -->
</div>
</body>
</html>
