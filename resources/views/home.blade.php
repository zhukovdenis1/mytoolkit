@extends('layouts.mtk')

@section('content')
    {!! $note->text !!}
    @if ($user_id == 1001)
        <hr>
        <ul>
            <li>
                <a href="/utils">Скрипты</a>
            </li>
        </ul>
    @endif

@endsection
