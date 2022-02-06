<?php
require_once('helpers.php');
require_once('data.php');
require_once('functions.php');

/* @var mysqli $CONNECTION - ссылка для соединения с базой данных
 * @var int $user_name - переменная имя пользователя
 * @var string $title - переменная title страницы
 * @var array $categories - массив для вывода категорий
 */

$categories = getCategories($CONNECTION);
foreach ($categories as $key => $category) {
    $categories[$key] = getSafeData($category);
};

$content = include_template('404-error.php', [
    'categories' => $categories,
]);

print include_template('layout.php', [
    'content' => $content,
    'categories' => $categories,
]);




