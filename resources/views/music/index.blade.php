@extends('layouts.mtk')

@section('content')
    @foreach($data as $artist)
        <h4>{{ $artist['name'] }}</h4>
        <ul>
            @foreach($artist['songs'] as $song)
                <li>
                    <a href="{{route('music.detail', ['song' => $song['id']])}}">{{ $song['title'] }}</a>
                </li>
            @endforeach
        </ul>
    @endforeach
@endsection
