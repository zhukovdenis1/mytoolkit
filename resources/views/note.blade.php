@extends('layouts.mtk')

@section('title', $note->title)
@section('keywords', $note->title)
@section('description', $note->title)

@section('content')
    <h1>{{$note->title}}</h1>
    <div class="note-menu">
        <ul>
            @foreach ($children as $item)
                @include('partials.note-tree-item', ['item' => $item])
            @endforeach
        </ul>
    </div>
    {!! $note->text !!}
@endsection
