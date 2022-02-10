<?php
require_once('helpers.php');
require_once('functions.php');
require_once('config.php');
require_once('data.php');
require_once('getwinner.php');

/* @var mysqli $CONNECTION - ссылка для соединения с базой данных
 * @var int $userName - переменная имя пользователя
 * @var string $title - переменная title страницы
 * @var array $categories - массив для вывода категорий
 */

$ads = getAds($CONNECTION);
$ads = formatAdsCardsData($ads);
$content = include_template('main.php', [
    'categories' => $categories,
    'ads' => $ads,
]);

print include_template('layout.php', [
    'title' => $title,
    'userName' => $userName,
    'content' => $content,
    'categories' => $categories,
]);


