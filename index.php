<?php
require_once('helpers.php');

$is_auth = rand(0, 1);
$title = 'Главная страница';
$user_name = 'Леонид';
$categories = ['Доски и лыжи', 'Крепления', 'Ботинки', 'Одежда', 'Инструменты', 'Разное'];
$ads = [
    [
        'title' => '2014 Rossignol District Snowboard',
        'category' => 'Доски и лыжи',
        'price' => '10999',
        'imgUrl' => 'img/lot-1.jpg',
        'expirationDate' => '2021-10-29',
    ],
    [
        'title' => 'DC Ply Mens 2016/2017 Snowboard',
        'category' => 'Доски и лыжи',
        'price' => '159999',
        'imgUrl' => 'img/lot-2.jpg',
        'expirationDate' => '2021-11-05',
    ],
    [
        'title' => 'Крепления Union Contact Pro 2015 года размер L/XL',
        'category' => 'Крепления',
        'price' => '8000',
        'imgUrl' => 'img/lot-3.jpg',
        'expirationDate' => '2021-11-03',
    ],
    [
        'title' => 'Ботинки для сноуборда DC Mutiny Charocal',
        'category' => 'Ботинки',
        'price' => '10999',
        'imgUrl' => 'img/lot-4.jpg',
        'expirationDate' => '2021-11-01',
    ],
    [
        'title' => 'Куртка для сноуборда DC Mutiny Charocal',
        'category' => 'Одежда',
        'price' => '7500',
        'imgUrl' => 'img/lot-5.jpg',
        'expirationDate' => '2021-11-07',
    ],
    [
        'title' => 'Маска Oakley Canopy',
        'category' => 'Разное',
        'price' => '5400',
        'imgUrl' => 'img/lot-6.jpg',
        'expirationDate' => '2021-11-19',
    ],
];

function formatAdPrice($price){
    $formatedPrice = number_format(ceil($price), 0, ',', ' ');
    return $formatedPrice . ' ₽';
};

function getTimeLeft ($expirationDate)
{
    $timeNow = date_create(date("Y-m-d H:i")); //2021-10-23
    $timeExpiration = date_create(/*$ads[0]['expirationDate']*/$expirationDate); //2021-11-25
    $intervalHours = str_pad(+date_interval_format(date_diff($timeNow, $timeExpiration),
            "%a") * 24 + +date_interval_format(date_diff($timeNow, $timeExpiration), "%H"), 2, "0", STR_PAD_LEFT);
    $intervalMinutes = str_pad(date_interval_format(date_diff($timeNow, $timeExpiration), "%i"), 2, "0", STR_PAD_LEFT);
    $timeLeft = $intervalHours . ":" . $intervalMinutes;
    return $timeLeft;
}


$content = include_template('main.php', [
    'categories' => $categories,
    'ads' => $ads,

]);
print include_template('layout.php', [
    'title' => $title,
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'content' => $content,
    'categories' => $categories,

]);


