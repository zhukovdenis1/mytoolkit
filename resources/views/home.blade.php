@extends('layouts.mtk')

@section('content')
    {!! $public !!}
    @if ($user_id == 1001)
        {!! $private !!}
    @endif
@endsection
