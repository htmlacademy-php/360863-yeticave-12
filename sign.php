<?php
require_once('helpers.php');
require_once('data.php');
require_once('functions.php');
require_once('validate-form.php');
require_once('config.php');

/* @var mysqli $CONNECTION - ссылка для соединения с базой данных
 * @var int $user_name - переменная имя пользователя
 * @var int $is_auth - переменная принимает рандомно значения 1 или 0
 * @var string $title - переменная title страницы
 * @var array $categories - массив для вывода категорий
 */

$requiredFields = [
    'email',
    'password',
    'name',
    'message',
];
$safeData = [];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $safeData = getSafeData($_POST, $CONNECTION);
    $errors = validateForm($requiredFields);

    $isEmailCompare = compareEmail($CONNECTION, $_POST['email']);
    if ($isEmailCompare){
        $errors['email'] = 'пользователь с таким именем уже зарегистрирован';
    }

    if (count($errors) === 0){
        insertPerson($CONNECTION, $safeData);

        foreach ($safeData as $key => $value) {
            $safeData[$key] = '';
        }
        header("Location: /login.php");
    }
}
$content = include_template('sign-up.php', [
    'errors' => $errors,
    'safeData' => $safeData,
]);

print include_template('layout.php', [
    'title' => $title,
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'content' => $content,
    'categories' => $categories,
]);