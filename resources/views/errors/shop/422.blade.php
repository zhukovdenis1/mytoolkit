@extends('errors::minimal')

@section('title', __('Validation Error'))
@section('code', '422')
@section('message')

    @if($errors->any())
        <div class="validation-errors">
            <h4>Validation Errors:</h4>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @else
        {{ __('Unprocessable Entity') }}
    @endif

@endsection
