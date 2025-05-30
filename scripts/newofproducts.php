<?php
try {
    $db1 = new PDO('mysql:host=localhost;dbname=undertaker_mtk',
        'undertaker',
        'superpuperadmin',
        array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'")
    );

    $db2 = new PDO('mysql:host=localhost;dbname=undertaker_ali',
        'undertaker',
        'superpuperadmin',
        array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'")
    );
} catch (PDOException $e) {
    die($e->getMessage());
}

$sth = $db1->prepare("SELECT * FROM `shop_products` ORDER BY id DESC LIMIT 10");
$sth->execute([]);
$data = $sth->fetchAll(PDO::FETCH_ASSOC);


foreach ($data as $d) {
    $p = [];
    $p['id_ae'] = $d['id_ae'];
    $p['id_adm'] = $d['id'];
    $p['category'] = $d['vk_category'];
    $p['category_id'] = $d['category_id'];
    $p['category_0'] = $d['category_0'];
    $p['category_1'] = $d['category_1'];
    $p['category_2'] = $d['category_2'];
    $p['category_3'] = $d['category_3'];
    $p['title_ae'] = $d['title_ae'];
    $p['title'] = $d['title'];
    $p['price'] = $d['price'];
    $p['price_from'] = $d['price_from'];
    $p['price_to'] = $d['price_to'];
    $p['description'] = $d['description'];
    $p['characteristics'] = $d['characteristics'];
    $p['photo'] = $d['photo'];
    $p['rating'] = $d['rating'];
    $p['moderated'] = 1;
    $p['parsed'] = 1;

    $query = saveQuery('ali_product', $p);

    $sth2 =$db2->prepare($query);
    $sth2->execute(array_values($p));
}

function saveQuery($table, $data)
{
    $keys = array();
    $values = array();

    foreach ($data as $k => $v)
    {
        array_push($keys, $k);
        array_push($values, '?');
    }

    $keyStr = '(' . implode(',', $keys) . ')';
    $valStr = '(' . implode(',', $values) . ')';

    $insertPart =  $keyStr . ' VALUES ' . $valStr;

    $query = 'INSERT INTO ' . $table . ' ' . $insertPart;

    return $query;
}
