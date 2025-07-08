<?php

/**
 * Для тестирования поставить debug=true
 * Сначала поставить debug_source = url и прописать debug_parse_url
 * Запустить 1 раз. Данные запишутся в файлы
 * Затем debug_source заменить на file
 */

//$uReviews = 'd8e734d3-0347-4a7e-a8eb-fe3826745659';
//$uReviews = 'd30d4e7e-1683-4300-b724-31fc418fdac7';
//$uChar = 'ae72b0f5-8ee3-4967-a5b5-8c84292fc0de';
//$uDesc = 'e1459484-97b0-4e41-a0be-e06fb8a0ff01';


return [
    'debug' => false,
    'debug_source' => 'file',//file or url
    'debug_file' => 'debug_product.txt',
    'debug_extra_file' => 'debug_extra.txt',
    'debug_extra2_file' => 'debug_extra2.txt',
    'debug_parse_url' => 'https://aliexpress.ru/item/1005008081521104.html',//если debug_source = url
    //'url' => 'https://mytoolkit.loc',
    'url' => 'https://deshevyi.ru',//'url' => 'https://mtk.deshevyi.ru',//урл для вызова скриптов
    'url_shop' => 'https://deshevyi.ru',
    'get_uri' => '/api/shop/get-product-for-parse',
    'set_uri' => '/api/shop/set-parsed-product',
    //'url_extra' => "https://aliexpress.ru/widget?uuid=$uReviews&&uuid=$uDesc&_bx-v=2.5.28",
    //'url_extra_2' => "https://aliexpress.ru/widget?uuid=$uReviews&uuid=$uChar&&uuid=$uDesc&_bx-v=2.5.28",
    //'url_extra' => "https://aliexpress.ru/widget?uuid=27a9f04d-c23c-4aa7-9446-572f753a5305&uuid=$uChar&uuid=$uReviews&uuid=eded288f-2f2b-4d12-b7b9-076f48dcc365&uuid=d30d4e7e-1683-4300-b724-31fc418fdac7&uuid=008d7ddf-ddb8-44ee-8f0b-49b779857027&uuid=fa76ca80-52f3-4bb3-95db-f46d96760bb5&uuid=c3eea9e2-c6a5-4239-9656-8ef38da58334&uuid=5e035d48-df37-4901-9711-977dca5b6da8&uuid=43946a0a-40f8-48e9-a5bd-5a53598e37db&uuid=55cedca7-d9f4-4a8e-96c4-b94df7f5cc66&uuid=ef9105ef-a550-433f-bdf2-0637bd47c32f&uuid=fed85114-3104-453b-bc5d-ca001922ebde&uuid=$uDesc&uuid=a5b4609f-cdac-4ad5-9bec-9cd518a056e6&uuid=f38e308c-ef6f-467b-b12c-e8e4dce2b728&uuid=8dec9aaf-2124-48d2-bf39-d390832c4152&uuid=ac237c57-23d7-42d7-8b08-3b78407e0045&_bx-v=2.5.28",
    //'url_extra_v2' => 'https://aliexpress.ru/widget?uuid=27a9f04d-c23c-4aa7-9446-572f753a5305&uuid=ae72b0f5-8ee3-4967-a5b5-8c84292fc0de&uuid=d8e734d3-0347-4a7e-a8eb-fe3826745659&uuid=d30d4e7e-1683-4300-b724-31fc418fdac7&uuid=008d7ddf-ddb8-44ee-8f0b-49b779857027&uuid=fa76ca80-52f3-4bb3-95db-f46d96760bb5&uuid=c3eea9e2-c6a5-4239-9656-8ef38da58334&uuid=5e035d48-df37-4901-9711-977dca5b6da8&uuid=43946a0a-40f8-48e9-a5bd-5a53598e37db&uuid=55cedca7-d9f4-4a8e-96c4-b94df7f5cc66&uuid=ef9105ef-a550-433f-bdf2-0637bd47c32f&uuid=fed85114-3104-453b-bc5d-ca001922ebde&uuid=e1459484-97b0-4e41-a0be-e06fb8a0ff01&uuid=3398848f-8cbc-4d62-81c8-d915468dde12&uuid=a5b4609f-cdac-4ad5-9bec-9cd518a056e6&uuid=f38e308c-ef6f-467b-b12c-e8e4dce2b728&uuid=8dec9aaf-2124-48d2-bf39-d390832c4152&uuid=ac237c57-23d7-42d7-8b08-3b78407e0045&_bx-v=2.5.28',
    'url_extra2' => 'https://aliexpress.ru/aer-jsonapi/v1/bx/pdp/web/productData',
    'coupons' => [
        'get_uri'  => '/api/shop/get-coupon-for-parse',
        'set_uri'  => '/api/shop/set-parsed-coupon',
        'pikabu_url' => 'https://promokod.pikabu.ru',
        'pikabu_debug_file' => 'debug_pikabu.txt',
    ],
    'reviews' => [
        'get_uri'  => '/api/shop/get-product-for-reviews-parse',
        'set_uri'  => '/api/shop/set-parsed-reviews',
        'set_tags_uri'  => '/api/shop/set-parsed-reviews-tags',
    ]
];

/**
 * https://aliexpress.ru/item/1005003392769891.html
 * debug_extra.txt: widget_by_uuid async widget not found: ae72b0f5-8ee3-4967-a5b5-8c84292fc0de
 */
