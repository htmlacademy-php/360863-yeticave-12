<?php
$connection = mysqli_connect("localhost", "root", "","yeticave");
mysqli_set_charset($connection, "utf8");

if ($connection == false){
    echo "ошибка подключения" . mysqli_connect_error();
}

$sql_lot = "SELECT lot.title as title, starting_price, completion_date, img, category.title as category, MAX(bid.sum)
FROM lot
JOIN category ON category.id = lot.category_id
LEFT JOIN bid ON lot.id = bid.lot_id
WHERE completion_date > now()
GROUP BY lot.title, starting_price, completion_date, img, lot.date_created_at
ORDER BY lot.date_created_at DESC";

$object_result_lot = mysqli_query($connection, $sql_lot);

if (!$object_result_lot){
    $error = mysqli_error($connection);
    echo "Ошибка MySQL: " . $error;
}

$ads = mysqli_fetch_all($object_result_lot, MYSQLI_ASSOC);


$sql_categories = "SELECT title, symbolic_code FROM category";
$object_result_categories = mysqli_query($connection, $sql_categories);
/*$records_count_categories = mysqli_num_rows($object_result_categories);
echo $records_count_categories;*/
$categories = mysqli_fetch_all($object_result_categories, MYSQLI_ASSOC);