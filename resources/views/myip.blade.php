@extends('layouts.mtk')

@section('title', 'My ip')
@section('keywords', 'My ip')
@section('description', 'My ip')

@section('content')
    <h1>Ваши данные</h1>
    <div style="font-size: 1.5em">
        <p>
            <strong>IP:</strong> {{$ip}}
        </p>
        <p>
            <strong>User Agent: </strong><br /><br /> {{$userAgent}}
        </p>
    </div>
@endsection
