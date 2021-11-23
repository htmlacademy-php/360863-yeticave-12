<?php
require_once('helpers.php');
require_once('functions.php');
require_once('config.php');
require_once('data.php');

$ads = getAds($CONNECTION);
$categories = getCategories ($CONNECTION);

foreach ($categories as $key => $category) {
    $categories[$key]['title'] = htmlspecialchars($category['title']);
    $categories[$key]['symbolic_code'] = htmlspecialchars($category['symbolic_code']);
};

foreach ($ads as $key => $ad) {
    $ads[$key]['category'] = htmlspecialchars($ad['category']);
    $ads[$key]['title'] = htmlspecialchars($ad['title']);
    $ads[$key]['starting_price'] = formatAdPrice(htmlspecialchars($ad['starting_price']));
    $ads[$key]['timeLeft'] = getTimeLeft(htmlspecialchars($ad['completion_date']));
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


