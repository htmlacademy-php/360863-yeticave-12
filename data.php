<?php
require_once('config.php');
$is_auth = rand(0, 1);
$title = 'Главная страница';
$user_name = 'Леонид';

$CONNECTION = mysqli_connect(HOST, USER, PASSWORD, DATABASE);
mysqli_set_charset($CONNECTION, "utf8");
if ($CONNECTION == false) {
    echo "ошибка подключения" . ' ' . mysqli_connect_error();
};

$categories = getCategories ($CONNECTION);
foreach ($categories as $key => $category){
    $categories[$key]['sectionClass'] = '';
}
foreach ($categories as $key => $category) {
    $categories[$key]['title'] = htmlspecialchars($category['title']);
    $categories[$key]['symbolic_code'] = htmlspecialchars($category['symbolic_code']);
    if (!empty($_POST['category'])){
        $categories[$key]['sectionClass'] = ($_POST['category'] === $category['id']) ? 'selected' : '';
    }

}

function getAds (object $link):array
{
    try {
        $sql_ads = "SELECT lot.id as lotId, lot.title as title, starting_price, completion_date, img, category.title as category, MAX(bid.sum) as current_price
FROM lot
JOIN category ON category.id = lot.category_id
LEFT JOIN bid ON lot.id = bid.lot_id
WHERE completion_date > now()
GROUP BY lot.id, lot.title, starting_price, completion_date, img, lot.date_created_at
ORDER BY lot.date_created_at DESC";

        $object_result_ads = mysqli_query($link, $sql_ads);
        if (!$object_result_ads) {
            throw new Error ('Ошибка объекта результата MySql:' . ' ' . mysqli_error($link));
        }
        return mysqli_fetch_all($object_result_ads, MYSQLI_ASSOC);
    } catch (Error $error){
        print($error);
        return [];
    }
}

function getCategories (mysqli $link):array
{
    try {
        $sql_categories = "SELECT id, title, symbolic_code FROM category";
        $object_result_categories = mysqli_query($link, $sql_categories);
        if (!$object_result_categories){
            throw new Error ('Ошибка объекта результата MySql:' . ' ' . mysqli_error($link));
        }
        return mysqli_fetch_all($object_result_categories, MYSQLI_ASSOC);
    } catch (Error $error) {
        print($error);
        return [];
    }
}

function getLot (object $link): ?array
{
    try {
        $sql_lot = "SELECT lot.id as id, lot.title as title, lot.description as description, starting_price, completion_date, img, category.title as category, MAX(bid.sum) as current_price, bid_step
FROM lot
JOIN category ON category.id = lot.category_id
LEFT JOIN bid ON lot.id = bid.lot_id
WHERE lot.id = ?
GROUP BY lot.id, lot.id, lot.title, lot.description, starting_price, completion_date, img, bid_step";
        $id = $_GET['id'];
        if (!$id) {
            throw new Error('id должен существовать, а он равен:' . ' ' . $id);
        }
        $idWithoutChars = preg_replace('/[^0-9]/', '', $id);
        if (strlen($id) !== strlen($idWithoutChars)) {
            throw new Error('id должен содержать только числа');
        }
        settype($id, 'integer');
        if ($id === 0) {
            throw new Error('id должен быть числом');
        }
        $stmt_lot = mysqli_prepare($link, $sql_lot);
        if ($stmt_lot === false) {
            throw new Error('Ошибка подготовленного выражения:' . ' ' . mysqli_error($link));
        }
        mysqli_stmt_bind_param($stmt_lot, 'i', $id);
        mysqli_stmt_execute($stmt_lot);
        $object_result_lot = mysqli_stmt_get_result($stmt_lot);
        return mysqli_fetch_assoc($object_result_lot);
    } catch (Error $error) {
        print($error);
        return [];
    }
}

function getbids (object $link):array
{
    try {
        $sql_bids = "SELECT bid.date_created_at, sum, person.name as name
FROM bid
JOIN person ON person_id = person.id
WHERE lot_id = ?
ORDER BY sum DESC";

        $id = $_GET['id'];
        if (!$id) {
            throw new Error('id должен существовать, а он равен:' . ' ' . $id);
        }
        $idWithoutChars = preg_replace('/[^0-9]/', '', $id);
        if (strlen($id) !== strlen($idWithoutChars)) {
            throw new Error('id должен содержать только числа');
        }
        settype($id, 'integer');
        if ($id === 0) {
            throw new Error('id должен быть числом');
        }
        $stmt_bids = mysqli_prepare($link, $sql_bids);
        if ($stmt_bids === false) {
            throw new Error('Ошибка подготовленного выражения:' . ' ' . mysqli_error($link));
        }
        mysqli_stmt_bind_param($stmt_bids, 'i', $id);
        mysqli_stmt_execute($stmt_bids);
        $object_result_bids = mysqli_stmt_get_result($stmt_bids);
        return mysqli_fetch_all($object_result_bids, MYSQLI_ASSOC);
    } catch (Error $error) {
        print($error);
        return [];
    }
}

function insertLot (mysqli $link, array $safeData): array
{
    try {
        if(!empty($_FILES)){
        $file_name = $_FILES['lot-img']['name'];
        $file_path = __DIR__ . '/uploads/';
        $imgUrlPost = '/uploads/' . $file_name;

        move_uploaded_file($_FILES['lot-img']['tmp_name'], $file_path . $file_name);
        }
        if ($_FILES['lot-img']['size'] === 0){
            $imgUrlPost = $_POST['img'];
        }
        $authorID = $_SESSION['user']['id'];

        $sql_insert_lot = "INSERT INTO lot SET
title = ?,
author_id = ?,
category_id = ?,
description = ?,
img = ?,
starting_price = ?,
bid_step = ?,
completion_date = ?";

        $safeData['lot-rate'] = mysqli_real_escape_string($link, (int) $safeData['lot-rate']);
        $safeData['lot-step'] = mysqli_real_escape_string($link, (int) $safeData['lot-step']);
        $stmt_insert_lot = mysqli_prepare($link, $sql_insert_lot);
        if ($stmt_insert_lot === false) {
            throw new Error('Ошибка подготовленного выражения:' . ' ' . mysqli_error($link));
        }
        mysqli_stmt_bind_param($stmt_insert_lot, 'sssssiis', $safeData['lot-name'], $authorID, $safeData['category'], $safeData['message'], $imgUrlPost,
            $safeData['lot-rate'], $safeData['lot-step'], $safeData['lot-date']);
        mysqli_stmt_execute($stmt_insert_lot);
        return [];
    } catch (Error $error) {
        print($error);
        return [];
    }
}

function insertPerson (mysqli $link, array $safeData): array
{
    try {
         $safeData['password'] = password_hash($safeData['password'], PASSWORD_BCRYPT);
         $sql_insert_person = "INSERT INTO person SET
email = ?,
name = ?,
password = ?,
contacts = ?";

        $stmt_insert_person = mysqli_prepare($link, $sql_insert_person);
        if ($stmt_insert_person === false) {
            throw new Error('Ошибка подготовленного выражения:' . ' ' . mysqli_error($link));
        }
        mysqli_stmt_bind_param($stmt_insert_person, 'ssss', $safeData['email'], $safeData['name'], $safeData['password'], $safeData['message']);
        mysqli_stmt_execute($stmt_insert_person);
        return [];
    } catch (Error $error) {
        print($error);
        return [];
    }
}

function getPersonData (mysqli $link, string $email): array
{
    $sql_email = "SELECT * FROM person WHERE email = '$email'";
    $object_result_email = mysqli_query($link, $sql_email);
    return mysqli_fetch_assoc($object_result_email);
}