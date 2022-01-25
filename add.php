<?php
require_once('helpers.php');
require_once('data.php');
require_once('functions.php');
require_once('validate-form.php');
require_once('config.php');

/* @var mysqli $CONNECTION - ссылка для соединения с базой данных
 * @var int $user_name - переменная имя пользователя
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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $safeData = getSafeData($_POST);


    if ($_FILES['lot-img']['size'] > 0) {
        $fileName = $_FILES['lot-img']['name'];
        $filePath = __DIR__ . '/uploads/';
        $imgUrlPost = $filePath . $fileName;
        move_uploaded_file($_FILES['lot-img']['tmp_name'], $filePath . $fileName);
        $imgValue = $filePath . $fileName;
    }

    $errors = validateForm($requiredFields, $safeData);

if (empty($errors)){
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
    'user_name' => $user_name,
    'content' => $content,
    'categories' => $categories,
]);
} else {
    http_response_code(403);

}