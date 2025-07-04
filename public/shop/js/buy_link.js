$('._aeLink').click(function() {
    let id = $(this).attr('data-id');
    let idae = $(this).attr('data-id_ae');
    let goUrl = '/go?id='+ id;

    $.colorbox({html:'<div style="margin:0 10px;text-align: center"><noindex>' +
            "<h2 style='margin:0;'>Ссылка на товар на Aliexpress: </h2>" +
            '<p><a onclick="goVisit(\'' + goUrl + '\');" class="_go" rel="nofollow" target="_blank" href="' + goUrl + '" style="font-size: 1.2em">https://aliexpress.ru/' + idae + '.html</a></p></noindex></div>'
    });
})
