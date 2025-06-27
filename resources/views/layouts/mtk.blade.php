<!doctype html>
<html lang="en">
<head>
    @inject('seo', 'App\Services\SeoService')
    {!! $seo->getNoIndexTag(view()->yieldContent('noindex'), request()) !!}
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="/img/favicon.png" type="image/png">
    <title>{{$seo->getTitle(view()->yieldContent('title'), request())}}</title>
    <meta name="description" content="{{$seo->getDescription(view()->yieldContent('description'), request())}}">
    <meta name="keywords" content="{{$seo->getKeywords(view()->yieldContent('keywords'), request())}}">

    <link href="/assets/index.css" charset="UTF-8" rel="stylesheet" type="text/css">
    @stack('css')
</head>
<body>
    @yield('css')
    <div class="wrapper">
        <div class="center-wrap">
            <header>
                <a href="/">Главная</a>
                <x-auth-link />
            </header>
            <main>
                @yield('content')
            </main>
        </div>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    @yield('js')
</body>
</html>
