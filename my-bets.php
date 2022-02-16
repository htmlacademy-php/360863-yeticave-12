<?php
require_once('init.php');
/*require_once('helpers.php');
require_once('functions.php');
require_once('config.php');
require_once('data.php');*/

/* @var mysqli $CONNECTION - ссылка для соединения с базой данных
 * @var int $userName - переменная имя пользователя
 * @var string $title - переменная title страницы
 * @var array $categories - массив для вывода категорий
 * @var array $safeUserData - массив данных залогиненного юзера
 */
if (!isset($_SESSION['user'])) {
    http_response_code(403);
} else {
    $safeData = getSafeData($_REQUEST);
    $userBids = getUserBids($CONNECTION, (int)$safeUserData['id']);
    if (!empty($userBids)) {
        $userBids = formatBetsData($CONNECTION, $userBids);
    }

    $content = include_template('bets-tmp.php', [
        'categories' => $categories,
        'userName' => $userName,
        'safeData' => $safeData,
        'userBids' => $userBids,
    ]);


    print include_template('layout.php', [
        'title' => $title,
        'userName' => $userName,
        'content' => $content,
        'categories' => $categories,
    ]);
}