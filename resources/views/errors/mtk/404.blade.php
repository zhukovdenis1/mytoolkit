@extends('layouts.mtk')

@section('title', 'Страница не найдена')

@section('content')
    <div class="container text-center">
        <h1>404</h1>
        <h2>Страница не найдена</h2>
        <p>Извините, но запрашиваемая вами страница не существует.</p>
        <a href="{{ url('/') }}" class="btn btn-primary">Вернуться на главную страницу</a>
    </div>
@endsection
