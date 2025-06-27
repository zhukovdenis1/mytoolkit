/*$(document).ready(function() {
    $('#songsScroll').click(function() {
        if ($(this).hasClass('_scrollOn')) {
            $('html, body').stop();
        } else {
            $('html, body').animate({
                scrollTop: $("footer").offset().top - $(window).height()
                },
                250000,
                function () {
                    //alert('finished');
                }
            );
        }
        $(this).toggleClass('_scrollOn');

    });

    $('html,body').scroll(function() {
        $(this).stop(true, false);
    });
});*/

var onlyOnceFlag = true;
var content = '';
var startKeyDiff = 0;

function changeKey(value, diff)
{
    if (value === null) {
        value = parseInt(document.getElementById('diff').textContent) + diff;
        value = (value < 12 && value > -12) ? value : 0;
    }

    document.getElementById('diff').textContent = value > 0 ? '+' + value : value;

    highlight(value);
}

function highlight(diff)
{
    var usedChords = [];
    diff = diff-startKeyDiff;
    let newContent = '';
    let lines = content.split("\n");
    let chordSymbols = ['A','B','C','D','E','F','G','H','m','#', '7','5','/']; //b не используется для простоты
    let chordsMajorSymbols = ['A', 'A#', 'B', 'C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#'];
    let chordFirstLetter = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];
    let chordsExtraSymbols = ['-','+','(',')','↓'];
    let allowedChordSymbols = [...chordSymbols, ...chordsExtraSymbols];

    for (let i = 0; i < lines.length; i++) {
        let line = lines[i];
        let strNoChord = [];
        let strAll = [];
        for (let j = 0; j < line.length; j++) {
            let char = line.charAt(j);
            if (char != ' ') {
                if (allowedChordSymbols.indexOf(char) >= 0) {

                } else {
                    strNoChord.push(char);
                }
                strAll.push(char);
            }
        }
//console.log(line);
        if (line[0] == '*') {
            newContent += '<span class="intro">' + line.substring(1) + '</span>';
        }
        else if (line == '---') {
            newContent += '<hr />';
        } else if (strAll.length > 0 && strNoChord.length == 0) {
            for (let i = chordsMajorSymbols.length-1; i >= 0; i--) {//важен обратный порядок
                let rIndex = i + diff;
                while (rIndex > 11) rIndex -= 12;
                while (rIndex < 0) rIndex += 12;
                let replacement = chordsMajorSymbols[rIndex];
                line = line.replaceAll(chordsMajorSymbols[i], `{${rIndex}}`);
            }
            chordsMajorSymbols.forEach((v, i) => {
                line = line.replaceAll(`{${i}}`, v);
            })

            let chordLine = line;
            chordsExtraSymbols.forEach((k) => {
                chordLine = chordLine.replaceAll(k, '');
            })
            chordLine.split(' ').forEach((v) => {
                if (v && usedChords.indexOf(v) < 0) {
                    usedChords.push(v);
                }
            });

            //line = line.replaceAll(' ', ' ');

            newContent += '<b class="chord">'+line+'</b>';
        } else {
            newContent += '<span class="word">'+line+'</span>';
        }
        newContent += "\n";
    }
    if (usedChords) {
        let usedChordsContent = `<div class="used-chords"> <div>Аккорды:</div><div>`;
        usedChords.forEach((k) => {
            usedChordsContent += '<span>' + k + '</span> ';
        })
        usedChordsContent += '</div></div>';

        newContent = usedChordsContent + newContent;
    }

    $('#song .chords').empty().html(newContent);
}

$(document).ready(function()
{
    if ($('#more').length) {

    } else {
        $('.chords').after('<div id="more"></div>');
    }
    if ($('.chords').length) {
        $('#more').append($('#mysongs').toggle());
    }

    $('.chords').after('<p><a href="#" onclick="return window.history.back()">&laquo; Назад</a></p>');

    if (onlyOnceFlag) {
        content = $('#song .chords').html();
        let chordsMajorSymbols = ['A', 'A#', 'B', 'C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#'];

        let keys = $('#song .chords').attr('data-key');
        keys = keys ? keys.trim().split(' ') : false;

        let keyContent = '';

        if (keys.length) {
            let currentKeyMajor = '';
            let origKeyMajor = '';

            keys.forEach((key, i) => {
                currentKeyMajor = i ? currentKeyMajor : key[0] + (key[1] == '#' ? '#' : '');
                origKeyMajor = (key[key.length-1] == '*') ? key[0] + (key[1] == '#' ? '#' : '') : origKeyMajor;
            });

            //Если не указана ориг. тональность, считам оригинальной первую
            origKeyMajor = origKeyMajor ? origKeyMajor : currentKeyMajor;

            let origKeyIndex = chordsMajorSymbols.indexOf(origKeyMajor);

            //console.log(currentKeyMajor, origKeyMajor);

            let keyData = [];
            let origKeyExist = false;
            keys.forEach((key, i) => {
                let majorChord = key[0]+ (key[1] == '#' ? '#' : '');
                let index = chordsMajorSymbols.indexOf(majorChord);
                let diff = index - origKeyIndex;
                diff = diff > 11 ? diff - 12 : diff;
                diff = diff < -11 ? diff +12 : diff;
                diff = diff > 3 ? diff - 12 : diff;//максимум +3, либо минус
                if (key[key.length-1] != '*') {
                    keyData.push({
                        diff: diff,
                        name: key
                    })
                } else {
                    origKeyExist = true;
                }

            })

            if (!keyData.length) {
                alert('Должна быть указана не только оригинальная тональность');
            }

            startKeyDiff = keyData[0].diff;

            keyContent += `<p class="keys">`
                + `<span id="down" onclick="changeKey(null, -1)" class="arrow">&darr;</span>`
                + `<span id="diff">${startKeyDiff}</span>`
                + `<span id="up" onclick="changeKey(null, 1)" class="arrow">&uarr;</span>`

            keyData.forEach((v, i) => {
                let className = (origKeyExist && !v.diff) ? 'key orig' : 'key';
                keyContent += `<span class="${className}" onclick="changeKey(${v.diff}, null)">${v.name}<sup>${v.diff ? v.diff : ''}</sup></span>`;
            })

            keyContent += '</p>';

            $('#song .chords').before(keyContent);

            highlight(startKeyDiff);
        } else {
            highlight(0);
        }
    }
    onlyOnceFlag = false
});


$(document).ready(function () {
    var page = $("html, body");

    $("#songsScroll" ).click(function(e) {
        $("#songsScroll" ).empty().text('X');

        page.on("scroll mousedown wheel DOMMouseScroll mousewheel keyup touchmove", function(){
            page.stop();
            $("#songsScroll" ).empty().html('&downdownarrows;');
        });
        
        var speedK = $('.chords').eq(0).attr('data-speed');

        var speedK = speedK ? speedK : 1;
        //var scrollSpeed =$('#scrollSpeed').attr('value');
        //scrollSpeed = scrollSpeed ? parseInt(scrollSpeed) : 150000;
        var scrollLenght = parseInt($("footer").offset().top - $(window).height());
        var scrollSpeed = parseInt(scrollLenght*80/speedK);
        //alert(scrollLenght);
        page.animate({ scrollTop:  scrollLenght}, scrollSpeed, "linear"/*function(){
                page.off("scroll mousedown wheel DOMMouseScroll mousewheel keyup touchmove");
                $("#songsScroll" ).empty().html('&downdownarrows;');
            }*/);

        return false;
    });

    var moreButton = $('<hr /><span class="more">&hellip;&hellip;&hellip;</span>').click(function() {
        $('#more').toggle();
    });
    $('#more').before(moreButton)
});