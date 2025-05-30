@extends('layouts.shop')

@section('title', 'Это интересно / Недорогой интернет-магазин')

@section('content')

    <h1>Интересно почитать</h1>

    {{-- Форма поиска --}}
    <form method="GET" action="">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Поиск по заголовку...">
            <button type="submit">
                Найти
            </button>

    </form>

    @if($articles->isEmpty())
        <p>Ничего не найдено</p>
    @else
        <ul class="ul">
        @foreach($articles as $article)
            <li><a href="{{route('article.detail', ['article' => $article, 'articleHru' => $article->uri])}}">{{$article->h1}}</a></li>
        @endforeach
        </ul>
    @endif

    @if($articles->hasPages())
        {{ $articles->withQueryString()->links() }}
    @endif

@endsection
