$(document).ready(function() {
    $('._hl, ._hl2').each(function() {
        var id = "id" + Math.random().toString(16).slice(2)
        $(this).attr('id', id);
        var mode = $(this).attr('mode');
        var cmode = '';
        switch (mode) {
            case 'php':
                cmode = 'application/x-httpd-php';
                break;
        }
        /*var html = $(this).html();
        var id = "id" + Math.random().toString(16).slice(2)
        $(this).empty();
        $(this).attr('id', id);

        CodeMirror(document.getElementById(id), {
            mode:  "text/x-php",
            lineNumbers: true,
            readOnly: true,
            value: html,
            theme: 'eclipse'
        });*/
        var CMParams = {
            lineNumbers: true,
            readOnly: true,
            theme: 'elegant'
            //theme: 'default'
        };

        if (cmode) CMParams.mode = cmode;

        if ($(this).hasClass('_hl2')) CMParams.theme = 'phpstorm';

        CodeMirror.fromTextArea(document.getElementById(id), CMParams);
    });


});