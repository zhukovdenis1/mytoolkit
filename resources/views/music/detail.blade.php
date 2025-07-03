@extends('layouts.mtk')

@section('title', $singer->name . '-' . $song->title)
@section('keywords', $singer->name . '-' . $song->title)
@section('description', $singer->name . '-' . $song->title)

@section('content')
    <div class="songsScroll" id="songsScroll">&downdownarrows;</div>
    <div id="song">
        <h1>{{$singer->name}} - {{$song->title}}</h1>
        <pre class="chords" data-key="{{$keys}}" data-speed="{{$song->speed ?: '1'}}">{{$song->text}}</pre>
    </div>
    @if($song->hidden_text)
        <div id="more">
            {{$song->hidden_text}}
        </div>
    @endif
@endsection

@section('js')
    <script src="/js/songs.js"></script>
@endsection

@section('css')
<style>
    .songsScroll {
        display: inline-block;
        border: 1px dashed;
        border-radius: 25px;
        padding: 15px 20px;
        color: #1C44F2;
        cursor: pointer;
        position: sticky;
        top: 10px;
        right: 10px;
        float: right;
    }

    #more {
        display: none;
    }
    .more {
        display: inline-block;
        color: #1C44F2;
        cursor: pointer;
        padding: 10px 0;
        font-size: 3em;
    }

    #more iframe {
        width: 100%;
        max-width: 700px;
        min-height: 315px;

    }

    pre {
        white-space: pre-wrap;
    }


    b.chord {
        color: #A5B3FAFF;
        background: #f6f6f6;
        margin-top: 20px;
        display: inline-block;
    }
    b.chord:first-line {
        color: #1C44F2;
        background: #eee;
    }

    span.word {
        color: #888;
        display: inline-block;
    }
    span.word:first-line {
        color: black;
    }

    #song .keys {
        display: flex;
        user-select: none;
    }
    #song .keys .arrow {
        display: inline-block;
        width: 2em;
        padding: 0.5em;
        background: #f6f6f6;
        border-radius: 20%;
        text-align: center;
        cursor: pointer;
        font-weight: bold;
    }
    #song .keys .arrow:hover {
        background: #999;
        color: #fff;
    }
    #song .keys #diff {
        display: inline-block;
        width: 3em;
        padding: 0.5em 0;
        text-align: center;

    }

    #song .keys .key {
        display: inline-block;
        border: 1px solid #e9e6e4;
        border-radius: 20%;
        margin: 0 0 0 2em;
        padding: 0 1em;
        cursor: pointer;
    }
    #song .keys .orig {
        text-decoration: underline;
    }
    #song .keys .key:hover {
        background: #999;
        color: #fff;
        border-color: #999;
    }
    .used-chords {
        margin: 15px 0;
        line-height: 1.5em;
    }
    .used-chords div:first-child {
        margin-right: 1em;
        margin-bottom: 1em;
    }
    #song .used-chords {
        display: flex;
        color: #777;
        flex-wrap: wrap;
    }
    #song .used-chords span {
        display: inline-block;
        border: 1px solid #e9e6e4;
        border-radius: 20%;
        margin: 0 0.7em 0 0;
        padding: 0 0.5em;
    }

    #song .intro {
        color: #ccc;

    }


    @media (max-width: 500px) {
        pre {
            width: 100%;
            overflow-x: scroll;
        }
        aside {display: none}
        /*.songsScroll {*/
        /*    position: fixed;*/
        /*    top: 20px;*/
        /*    right:0;*/
        /*    margin:0;*/
        /*}*/

        .panel {
            display: none;
        }
    }
</style>
@endsection
