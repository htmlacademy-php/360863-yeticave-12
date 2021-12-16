<?php
require_once('helpers.php');
require_once('data.php');
require_once('config.php');

/* @var mysqli $CONNECTION - ссылка для соединения с базой данных
 * @var int $user_name - переменная имя пользователя
 * @var int $is_auth - переменная принимает рандомно значения 1 или 0
 * @var string $title - переменная title страницы
 * @var array $categories - массив для вывода категорий
 */

$content = include_template('login-temp.php', [

]);

print include_template('layout.php', [
    'title' => $title,
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'content' => $content,
    'categories' => $categories,
]);