<?php
require_once('helpers.php');
require_once('data.php');

$categories = getCategories ($CONNECTION);
foreach ($categories as $key => $category) {
    $categories[$key]['title'] = htmlspecialchars($category['title']);
    $categories[$key]['symbolic_code'] = htmlspecialchars($category['symbolic_code']);
};

$content = include_template('404-error.php', [
]);

print include_template('layout.php', [
    'content' => $content,
    'categories' => $categories,
]);