<?php
require_once('init.php');
/*require_once('helpers.php');
require_once('data.php');
require_once('config.php');
require_once('validate-form.php');
require_once('functions.php');*/

/* @var mysqli $CONNECTION - ссылка для соединения с базой данных
 * @var int $userName - переменная имя пользователя
 * @var string $title - переменная title страницы
 * @var array $categories - массив для вывода категорий
 */


$requiredFields = [
    'email',
    'password',
];
$safeData = [];
$errors = [];
$errorsLogin = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $safeData = getSafeData($_POST);
    $errors = validateForm($requiredFields, $safeData);
    $errorsLogin = checkLoginData($CONNECTION, $safeData['email'], $safeData['password'], $errors);
    if (empty($errors) && empty($errorsLogin)) {
        $_SESSION['user'] = getPersonData($CONNECTION, $safeData['email']);
        header("Location: /index.php");
        $safeData = [];
    }
}

$content = include_template('login-temp.php', [
    'errors' => $errors,
    'errorsLogin' => $errorsLogin,
    'safeData' => $safeData,
    'categories' => $categories,

]);

print include_template('layout.php', [
    'title' => $title,
    'userName' => $userName,
    'content' => $content,
    'categories' => $categories,
]);