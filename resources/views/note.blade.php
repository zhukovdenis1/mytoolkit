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

@push('css')
    <!--link rel=stylesheet href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.21.0/codemirror.min.css" /-->
    <link rel=stylesheet href="/css/codemirror/codemirror.min.css" />
    <link rel=stylesheet href="/css/codemirror/codemirror-darcula.css" />
@endpush

@section('js')
    <!--script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.21.0/codemirror.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.21.0/addon/edit/matchbrackets.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.21.0/addon/runmode/runmode.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.21.0/addon/runmode/colorize.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.21.0/addon/edit/closetag.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.21.0/mode/javascript/javascript.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.21.0/mode/xml/xml.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.21.0/mode/css/css.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.21.0/mode/htmlmixed/htmlmixed.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.21.0/mode/php/php.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.21.0/mode/clike/clike.min.js"></script-->
    <script src="/js/codemirror-5.21.0/codemirror.min.js"></script>
    <script src="/js/codemirror-5.21.0/addon/edit/matchbrackets.js"></script>
    <script src="/js/codemirror-5.21.0/addon/runmode/runmode.min.js"></script>
    <script src="/js/codemirror-5.21.0/addon/runmode/colorize.min.js"></script>
    <script src="/js/codemirror-5.21.0/addon/edit/closetag.js"></script>
    <script src="/js/codemirror-5.21.0/mode/javascript.min.js"></script>
    <script src="/js/codemirror-5.21.0/mode/xml.min.js"></script>
    <script src="/js/codemirror-5.21.0/mode/css.min.js"></script>
    <script src="/js/codemirror-5.21.0/mode/htmlmixed.min.js"></script>
    <script src="/js/codemirror-5.21.0/mode/php.min.js"></script>
    <script src="/js/codemirror-5.21.0/mode/clike.min.js"></script>
    <script src="/js/highlight.js"></script>
@endsection
