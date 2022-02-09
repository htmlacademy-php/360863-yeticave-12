<?php
require_once('helpers.php');
require_once('functions.php');
require_once('config.php');
require_once('data.php');
require_once('helpers.php');

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
    foreach ($userBids as $key => $userBid) {
        $userBids[$key] = prepareData($userBid);
    }

    foreach ($userBids as $key => $userBid) {
        $userBids[$key]['time_passed'] = getTimePassed($userBid['bidDate']);
        $userBids[$key]['lastBidUserId'] = getLastBidUserId($CONNECTION, (int)$userBid['lotId']);
        if ($userBids[$key]['timeLeft']["hoursLeft"] === '00') {
            $userBids[$key]['timerClass'] = 'timer--finishing';
            $userBids[$key]['timerText'] = $userBid['timeLeft']['hoursLeft'] . ':' . $userBid['timeLeft']['minutesLeft'];

        } elseif (strtotime($userBids[$key]['completion_date']) <= strtotime("now") && (int)$userBids[$key]['lastBidUserId']['person_id'] == (int)$userBids[$key]['person_id']) {
            $userBids[$key]['timerClass'] = 'timer--win';
            $userBids[$key]['timerText'] = 'Ставка выиграла';
            $userBids[$key]['userContacts'] = $userBid['contacts'];
        } elseif (strtotime($userBids[$key]['completion_date']) <= strtotime("now") && (int)$userBids[$key]['lastBidUserId']['person_id'] != (int)$userBids[$key]['person_id']) {
            $userBids[$key]['timerClass'] = 'timer--end';
            $userBids[$key]['timerText'] = 'Торги окончены';
        } else {
            $userBids[$key]['timerClass'] = ' ';
            $userBids[$key]['timerText'] = $userBid['timeLeft']['hoursLeft'] . ':' . $userBid['timeLeft']['minutesLeft'];
        }
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






