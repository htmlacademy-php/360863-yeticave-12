<?php
require_once('database.php');

$CONNECTION = mysqli_connect($dataBase['host'], $dataBase['userName'], $dataBase['password'], $dataBase['database']);
mysqli_set_charset($CONNECTION, "utf8");
if ($CONNECTION == false) {
    echo "ошибка подключения" . mysqli_connect_error();
};

function getAds ($link)
{
    $sql_ads = "SELECT lot.id as lotId, lot.title as title, starting_price, completion_date, img, category.title as category, MAX(bid.sum) as current_price
FROM lot
         JOIN category ON category.id = lot.category_id
         LEFT JOIN bid ON lot.id = bid.lot_id
WHERE completion_date > now()
GROUP BY lot.id, lot.title, starting_price, completion_date, img, lot.date_created_at
ORDER BY lot.date_created_at DESC";

    $object_result_ads = mysqli_query($link, $sql_ads);

    if (!$object_result_ads) {
        $error = mysqli_error($link);
        return print ("Ошибка MySQL: " . $error);
    } else {
        return mysqli_fetch_all($object_result_ads, MYSQLI_ASSOC);
    }
}

function getCategories ($link) {
    $sql_categories = "SELECT title, symbolic_code FROM category";
    $object_result_categories = mysqli_query($link, $sql_categories);

    if (!$object_result_categories){
        $error = mysqli_error($link);
        return print ("Ошибка MySQL: " . $error);
    } else {
        return mysqli_fetch_all($object_result_categories, MYSQLI_ASSOC);
    }
}

function getLot ($link){
    $lotId = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

    $sql_lot = "SELECT lot.id as lotId, lot.title as title, lot.description as description, starting_price, completion_date, img, category.title as category, MAX(bid.sum) as current_price, bid_step
FROM lot
JOIN category ON category.id = lot.category_id
LEFT JOIN bid ON lot.id = bid.lot_id
WHERE lot.id = $lotId";

    $object_result_lot = mysqli_query($link, $sql_lot);

    if (!$object_result_lot) {
        $error = mysqli_error($link);
        return print ("Ошибка MySQL: " . $error);
    } else {
        return mysqli_fetch_assoc($object_result_lot);
    }
}

function getbids ($link){
    $lotId = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

    $sql_bids = "SELECT bid.date_created_at, sum, person.name as name
FROM bid

JOIN person ON person_id = person.id
WHERE lot_id = $lotId
ORDER BY sum DESC";

    $object_result_bids = mysqli_query($link, $sql_bids);

    if (!$object_result_bids) {
        $error = mysqli_error($link);
        return print ("Ошибка MySQL: " . $error);
    } else {
        return mysqli_fetch_all($object_result_bids, MYSQLI_ASSOC);
    }
}
