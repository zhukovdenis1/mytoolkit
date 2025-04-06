<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @yield('meta')
    <meta name="description" content="Недорогой интернет-магазин DealExtreme. Посредник DealExtreme по России и СНГ. DealExtreme - китайский интернет-магазин дешевых товаров. Основным преимуществом DealExtreme помимо низких цен является бесплатная доставка по всему миру." />
    <meta name="keywords" content="Недорогой интернет-магазин DealExtreme, Dealextreme на русском, dialextrim, dealextream, дешевый интернет-магазин, недорогой интернет-магазин, интернет магазин китайских товаров с бесплатной доставкой, китайские интернет магазины на русском языке, dealextreme аналог Россия СНГ, китайский интернет магазин на русском" />
    <link rel="stylesheet" href="/shop/css/main.css" />
    <link rel="stylesheet" href="/shop/css/colorbox.css" />
    @stack('css')
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
                <div class="account"><a href="https://deshevyi.ru/?r=2&aid=0&st={login}" rel="nofollow">Войти</a><br /><a href="https://deshevyi.ru/?r=2&aid=0&st={login}" rel="nofollow">Регистрация</a></div>
                <a class="wish" href="https://deshevyi.ru/?r=2&aid=0&st={wishlist}" rel="nofollow">Мои<br />желания</a>
                <a class="cart" href="https://deshevyi.ru/?r=2&aid=0&st={basket}" rel="nofollow"><span class="counter">0</span>Корзина</a>
            </div>
        </header>
        <main>
            <article>
                @if(!Route::is('home')) {{ Breadcrumbs::render() }} @else <ol class="brcr"></ol> @endif
                @yield('content')
            </article>
            <!--aside>Asided</aside-->

        </main>
        <footer>&copy;{{ date('Y') }}</footer>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="/js/jquery.colorbox-min.js"></script>

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
