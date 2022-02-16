<?php
require_once('init.php');

/* @var mysqli $CONNECTION - ссылка для соединения с базой данных
 * @var int $userName - переменная имя пользователя
 * @var string $title - переменная title страницы
 * @var array $categories - список категорий
 */

if (isset($_SESSION['user'])) {
    $requiredFields = [
        'lot-name',
        'category',
        'message',
        'lot-rate',
        'lot-step',
        'lot-date',
    ];
    $errors = [];
    $safeData = [];
    $imgValue = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $safeData = getSafeData($_POST);

        if ($_FILES['lot-img']['size'] > 0) {
            $imgValue = getFileName();
        }

        $errors = validateForm($requiredFields, $safeData);

        if (empty($errors)) {
            insertLot($CONNECTION, $safeData);
            $imgValue = '';
            $safeData = [];
            $categories = [];

            header("Location: /index.php");
        }
    }

    $content = include_template('add-lot.php', [
        'categories' => $categories,
        'errors' => $errors,
        'safeData' => $safeData,
        'imgValue' => $imgValue,
    ]);

    print include_template('layout.php', [
        'title' => $title,
        'userName' => $userName,
        'content' => $content,
        'categories' => $categories,
    ]);
} else {
    http_response_code(403);

}