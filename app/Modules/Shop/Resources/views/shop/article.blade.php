@extends('layouts.shop')

@section('meta')
    <title>{{ $article->title ?? $article->name }}</title>
    <meta name="description" content="{{ $article->title ?? $article->name }}" />
    <meta name="keywords" content="{{ $article->title ?? $article->name }}" />
@endsection

@section('content')
    <h1>{{$article->name}}</h1>
    {!! $article->text !!}
@endsection

