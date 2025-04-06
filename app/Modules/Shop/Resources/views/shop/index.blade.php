@extends('layouts.shop')

@section('meta')
    <title>{{ $title }}</title>
    <meta name="description" content="{{ $title }}. Посредник DealExtreme по России и СНГ. DealExtreme - китайский интернет-магазин дешевых товаров. Основным преимуществом DealExtreme помимо низких цен является бесплатная доставка по всему миру." />
    <meta name="keywords" content="{{ $title }}c, Dealextreme на русском, dialextrim, dealextream, дешевый интернет-магазин, недорогой интернет-магазин, интернет магазин китайских товаров с бесплатной доставкой, китайские интернет магазины на русском языке, dealextreme аналог Россия СНГ, китайский интернет магазин на русском" />
@endsection

@section('content')
    <div class="text-center">
        @if ($products->isEmpty())
            {{__('Не найдено')}}
        @else
            <ul class="prod-list" id="prodList">
            @foreach($products as $product)
                <li>
                    <a href="{{ route('detail', ['productId' =>$product->id, 'productHru'=>$product->hru]) }}">
                        <span class="img-wrap">
                            <x-shop::preview :product="$product" />
                            <div class="bg"></div>
                        </span>
                        <span class="free">
                            <svg width="17" height="14" viewBox="0 0 17 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15.6851 8.121L13.579 6.905L12.933 4.48401C12.7 3.60901 11.907 3 11 3H9.60706L9.19995 1.48199C8.96595 0.607995 8.17394 0 7.26794 0H2C0.896 0 0 0.896 0 2V10C0 11.104 0.896 12 2 12H15C15.971 12 16.801 11.303 16.97 10.348C17.139 9.39199 16.5311 8.562 15.6851 8.121Z" fill="#00AD30"></path><path d="M4.5 14C5.88071 14 7 12.8807 7 11.5C7 10.1193 5.88071 9 4.5 9C3.11929 9 2 10.1193 2 11.5C2 12.8807 3.11929 14 4.5 14Z" fill="white"></path><path d="M4.5 13C5.32843 13 6 12.3284 6 11.5C6 10.6716 5.32843 10 4.5 10C3.67157 10 3 10.6716 3 11.5C3 12.3284 3.67157 13 4.5 13Z" fill="#00AD30"></path><path d="M12.5 14C13.8807 14 15 12.8807 15 11.5C15 10.1193 13.8807 9 12.5 9C11.1193 9 10 10.1193 10 11.5C10 12.8807 11.1193 14 12.5 14Z" fill="white"></path><path d="M12.5 13C13.3284 13 14 12.3284 14 11.5C14 10.6716 13.3284 10 12.5 10C11.6716 10 11 10.6716 11 11.5C11 12.3284 11.6716 13 12.5 13Z" fill="#00AD30"></path><path d="M12.7709 7.76099L11.9658 4.742C11.8488 4.304 11.4509 4 10.9989 4H8.83984L9.53088 6.582L12.7709 7.76099Z" fill="white"></path></svg>
                            <span>бесплатно</span>
                        </span>

                        <x-shop::rating :rating="$product->rating" />
                        <span class="title">{{$product->title ?: $product->title_ae}}</span>
                        <span class="price"><x-shop::price :product="$product" /></span>
                    </a>
                </li>
            @endforeach
            </ul>
            <div class="loading" id="loading"></div>
        @endif
    </div>
    @if(Route::is('home'))
    <div class="home-article">
        <h1>Недорогой интернет-магазин</h1>
        <p>Если у вас не хватает времени, чтобы проводить большую часть выходного для в Торговых Центрах, в поисках той или иной вещи, то&nbsp;<strong>DealExtreme - интернет магазин на русском языке</strong>&nbsp;именно для вас. Делая заказ на этом сайте, вы не потратите лишнего времени, вам не нужно никуда выезжать из дома и тратить деньги на поездки из одного магазина в другой.</p>
        <br>
        <p><span style="line-height: 1.6em;">На официальном сайте можно выбирать так же тщательно, как во время обычного шопинга. К примеру, если вы покупаете одежду в интернет-магазине, здесь также можно подобрать товар по размеру, по цвету, по материалу, из которого вещь пошита. Выбирать и совершать покупки лежа на диване, не выходя из дома - это еще один плюс ресурса Ебей. Причем покупки можно совершать в любое время. Например, у вас есть час обеденного перерыва, часть этого времени можно потратить на шопинг в&nbsp;Интернет-магазин Dealextreme на русском языке.</span></p>
        <br>
        <p><span style="line-height: 1.6em;">Наш <strong>дешевый интернет-магазин</strong> с бесплатной доставкой по всей России позволит вам не связываться с почтовыми работниками, вам не надо будет долго объяснять сотруднику магазина, что именно вы хотите купить крутые вещи на алиэкспресс. Сотрудники интернет-ресурса настоящие профессионалы, они работают индивидуально с каждым клиентом. Сайт виртуальной витрины eBay дает вам гарантию. Благодаря опыту специалистов магазина, можно выяснить все подробности, задать дополнительные вопросы, если таковые появятся.</span></p>
        <br>
        <p><span style="line-height: 1.6em;">На нашем интернет-магазине заказы от китайских продавцов - это надежно и быстро. Заказы принимаются от всех жителей СНГ. На сайте отличная навигация, где за считанные минуты можно найти, то что нужно. С помощью&nbsp;Интернет-магазина Ебей на русском языке, можно отлично экономить. Если вы азартны, то здесь есть возможность не только экономить, но и поучаствовать в аукционе, при этом получив удовольствие от победы в этом рисковом мероприятии. Вы сможете найти эксклюзивные и неординарные предметы, здесь представлены товары от известных брендов, которые невозможно увидеть в обычных магазинах, а некоторые предметы будут привлекательны для истинных коллекционеров. В нашем дешёвом интернет-магазине представлен товары разных категорий: компьютерные комплектующие, электроника, вы можете купить бензиновые зажигалки, запчасти для фонарей светодиодных, комплектующие для пк, масло для электронной сигареты, электро зажигалки для сигарет </span></p>
        <br>
        <p><span style="line-height: 1.6em;">Как происходит доставка?</span></p>
        <br>
        <ul>
            <li>менеджер уточнит и проверит детали Вашей заявки</li>
            <li>затем закажет вещь у продавца.</li>
            <li>товар приходит на склад, затем со склада его отправляют по почте, либо курьером, магазин пользуется помощью международных почтовых компаний при доставке. Перед тем, как товар отправляется на почту или перед тем, как его заберет курьер, проводят проверку изделий на качество, а именно: есть ли брак, на соответствие заказанного цвета, размера, соответствует ли выбранное Вами описанию.</li>
        </ul>
        <br>
        <p>Оплату можно произвести деньгами с электронных кошельков, принимается и банковская карточка. На eBay заниматься шопингом легко и просто, быстро и удобно. Заходите на сайт и убедитесь в этом.</p>
    </div>
    @endif
@endsection

@section('js')
    <script>
        var activeRequest = false;
        var page = 1;
        $(document).ready(function() {
            showMore();
        });
        $(window).scroll(function() {
            showMore();
        });

        function showMore() {
            //if($(window).scrollTop() + $(window).height() >= $(document).height()) {
            if ($("#loading").length > 0 && $(window).scrollTop() + $(window).height() > $('#loading').offset().top) {
                $('#loading').css('visibility', 'visible');
                if (!activeRequest) {
                    activeRequest = true;
                    page++;
                    $.ajax({
                        url: '/more',
                        data: {page: page, categoryId: '{{ $category }}', search: '{{ $search }}'},
                        dataType: 'html',
                        success: function(data){
                            if (data.length == 0) {
                                $('#loading').remove();
                            }
                            $('#prodList').append(data);
                            if ($(window).scrollTop() + $(window).height() > $('#loading').offset().top) {
                                $('html, body').animate({
                                    scrollTop: $("#loading").offset().top-$(window).height()-10
                                }, 1000);
                            }
                            activeRequest = false;
                            $('#loading').css('visibilite','hidden');
                        },
                        error: function() {
                            activeRequest = false;
                            $('#loading').css('visibility', 'visible');
                        }
                    });
                }

            }
        }
    </script>
@endsection
