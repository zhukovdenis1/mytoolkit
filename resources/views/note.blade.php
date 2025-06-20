@extends('layouts.mtk')

@section('title', $note->title)
@section('keywords', $note->title)
@section('description', $note->title)

@section('content')
    <h1>{{$note->title}}</h1>
    {!! $note->text !!}
@endsection
