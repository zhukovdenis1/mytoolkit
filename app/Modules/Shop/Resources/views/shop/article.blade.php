@extends('layouts.shop')

@section('title', $article->title ?? $article->h1)
@section('keywords', $article->keyworkds)
@section('description', $article->description)

@section('content')
    <div class="article">
        <h1>{{$article->h1}}</h1>
        {!! $article->text !!}
    </div>
@endsection

