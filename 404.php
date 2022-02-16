<?php
require_once('init.php');

/**
 * @var array $categories - массив для вывода категорий
 */


$content = include_template('404-error.php', [
    'categories' => $categories,
]);

print include_template('layout.php', [
    'content' => $content,
    'categories' => $categories,
]);




