<?php
require_once('helpers.php');
require_once('data.php');
require_once('functions.php');
require_once('validate-form.php');
require_once('config.php');

/* @var mysqli $CONNECTION - ссылка для соединения с базой данных
 * @var int $userName - переменная имя пользователя
 * @var string $title - переменная title страницы
 * @var array $categories - массив для вывода категорий
 */
if (isset($_SESSION['user'])) {
    http_response_code(403);
} else {
    $requiredFields = [
        'email',
        'password',
        'name',
        'message',
    ];
    $safeData = [];
    $errors = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $safeData = getSafeData($_POST);
        $errors = validateForm($requiredFields, $safeData);
        $isEmailCompare = compareEmail($CONNECTION, $safeData['email']);

        if ($isEmailCompare) {
            $errors['email'] = 'пользователь с таким email уже зарегистрирован';
        }

        if (empty($errors)) {
            insertPerson($CONNECTION, $safeData);
            $safeData = [];
            header("Location: /login.php");
        }
    }
    $content = include_template('sign-up.php', [
        'errors' => $errors,
        'safeData' => $safeData,
        'categories' => $categories,
    ]);

    print include_template('layout.php', [
        'title' => $title,
        'userName' => $userName,
        'content' => $content,
        'categories' => $categories,
    ]);
}