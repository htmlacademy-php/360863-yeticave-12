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
    ],
    [
        'title' => 'DC Ply Mens 2016/2017 Snowboard',
        'category' => 'Доски и лыжи',
        'price' => '159999',
        'imgUrl' => 'img/lot-2.jpg',
    ],
    [
        'title' => 'Крепления Union Contact Pro 2015 года размер L/XL',
        'category' => 'Крепления',
        'price' => '8000',
        'imgUrl' => 'img/lot-3.jpg',
    ],
    [
        'title' => 'Ботинки для сноуборда DC Mutiny Charocal',
        'category' => 'Ботинки',
        'price' => '10999',
        'imgUrl' => 'img/lot-4.jpg',
    ],
    [
        'title' => 'Куртка для сноуборда DC Mutiny Charocal',
        'category' => 'Одежда',
        'price' => '7500',
        'imgUrl' => 'img/lot-5.jpg',
    ],
    [
        'title' => 'Маска Oakley Canopy',
        'category' => 'Разное',
        'price' => '5400',
        'imgUrl' => 'img/lot-6.jpg',
    ],
];

function formatAdPrice($price){
    $formatedPrice = number_format(ceil($price), 0, ',', ' ');
    return $formatedPrice . ' ₽';
};

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


