<?php
require_once('helpers.php');
require_once('data.php');
require_once('config.php');
require_once('validate-form.php');
require_once('functions.php');

/* @var mysqli $CONNECTION - ссылка для соединения с базой данных
 * @var int $user_name - переменная имя пользователя
 * @var int $is_auth - переменная принимает рандомно значения 1 или 0
 * @var string $title - переменная title страницы
 * @var array $categories - массив для вывода категорий
 */


$requiredFields = [
    'email',
    'password',
];
$safeData = [];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $safeData = getSafeData($_POST);
    $errors = validateForm($requiredFields, $safeData);

    if (empty($errors)){

        $isEmailCompare = compareEmail($CONNECTION, $safeData['email']);
        if (!$isEmailCompare){
            $errors['email'] = 'пользователь с таким email не найден';
        }

        if (comparePassword($CONNECTION, $safeData['email'], $safeData['password'])){
            $_SESSION['user'] = getPersonData($CONNECTION, $safeData['email']);
            header("Location: /index.php");
            $safeData = [];
        } else {
            $errors['password'] = 'Вы ввели неверный пароль';
        }
    }
}

$content = include_template('login-temp.php', [
    'errors' => $errors,
    'safeData' => $safeData,
    'categories' => $categories,

]);

print include_template('layout.php', [
    'title' => $title,
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'content' => $content,
    'categories' => $categories,
]);