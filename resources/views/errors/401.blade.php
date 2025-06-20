@extends('layouts.mtk')

@section('title', 'Доступ запрещён')

@section('content')
    <div class="container text-center">
        <h1>403</h1>
        <h2>Доступ запрещён</h2>
        <p><a href="{{ url('/login') }}" class="btn btn-primary">Авторизоваться</a></p>
        <a href="{{ url('/') }}" class="btn btn-primary">Вернуться на главную страницу</a>
    </div>
@endsection
