@extends('layouts.mtk')

@section('title', 'Aliexpress')

@section('content')
    <h1>Aliexpress</h1>
    <form action="" method="post">
        @csrf
        <textarea name="content" style="width: 100%; height: 100px"></textarea>
        <input type="submit" value=">">
    </form>
    <pre>{!! $content !!}</pre>
@endsection
