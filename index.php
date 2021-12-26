<?php
require_once('helpers.php');
require_once('functions.php');
require_once('config.php');
require_once('data.php');

/* @var mysqli $CONNECTION - ссылка для соединения с базой данных
 * @var int $user_name - переменная имя пользователя
 * @var string $title - переменная title страницы
 * @var array $categories - массив для вывода категорий
 * @var string $searchWord - Поисковой запрос
 */

$ads = getAds($CONNECTION);

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
    'user_name' => $user_name,
    'content' => $content,
    'categories' => $categories,
    'searchWord' => $searchWord,
]);


