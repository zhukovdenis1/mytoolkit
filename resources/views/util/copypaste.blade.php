@extends('layouts.mtk')

@section('title', 'CopyPaste')

@section('content')
    <h1>Copypaste</h1>
    <form action="" method="post">
        @csrf
        <textarea id="text" name="content" style="width: 100%;height: 50px;">{{$content}}</textarea>
        <button name="save" value="1">Сохранить</button>
        <button name="reset" value="1">Очистить</button>
        <button onclick="copyText()">Скопировать</button>
    </form>

    <script type="text/javascript">
        function copyText() {
            var copyText = document.getElementById("text");
            copyText.select();
            document.execCommand("copy");
        }
    </script>
@endsection
