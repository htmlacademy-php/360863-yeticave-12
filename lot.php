<?php
require_once('helpers.php');
require_once('functions.php');
require_once('config.php');
require_once('data.php');
require_once('helpers.php');
require_once('validate-form.php');

/* @var mysqli $CONNECTION - ссылка для соединения с базой данных
 * @var int $userName - переменная имя пользователя
 * @var string $title - переменная title страницы
 * @var array $categories - массив для вывода категорий
 * @var array $safeUserData - массив данных залогиненного юзера
 */

$safeData = getSafeData($_REQUEST);

if (empty($safeData['id'])) {
    $content = include_template('404-error.php', [
        'categories' => $categories,
    ]);
    http_response_code(404);
} else {
    $lot = getLot($CONNECTION, $safeData['id']);
    if (empty($lot['id'])) {
        $content = include_template('404-error.php', [
            'categories' => $categories,
        ]);
        http_response_code(404);
    } else {

        $lot = prepareData($lot);
        $lastBidUserId = getLastBidUserId($CONNECTION, $lot['id']);
        $isTakeBidsVisible = true;
        if (
            empty($userName)
            || strtotime($lot['completion_date']) <= strtotime('now')
            || (int)$lot['authorId'] == (int)$safeUserData['id']
            || (!empty($lastBidUserId['person_id']))
            && (int)$safeUserData['id'] == (int)$lastBidUserId['person_id']
        ) {
            $isTakeBidsVisible = false;
        }

        $bids = getBids($CONNECTION);
        $errors = [];
        $lotPrice = (int)str_replace(' ', '', $lot['price']);
        $bidStep = (int)$lot['bid_step'];
        $requiredFields = [
            'cost',
        ];
        if (!empty($bids)){
            $bids = formatBidsData($bids);
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $errors = validateCost($requiredFields, $safeData, $lotPrice, $bidStep);
            if (empty($errors)) {
                insertBid($CONNECTION, $safeData['cost'], (int)$safeUserData['id'], (int)$lot['id']);
                $safeData = [];
                header("Location: /lot.php?id=" . $lot['id']);
            }
        }

        $content = include_template('lot-item.php', [
            'categories' => $categories,
            'lot' => $lot,
            'bids' => $bids,
            'userName' => $userName,
            'errors' => $errors,
            'safeData' => $safeData,
            'isTakeBidsVisible' => $isTakeBidsVisible
        ]);
    }
}

print include_template('layout.php', [
    'title' => $title,
    'userName' => $userName,
    'content' => $content,
    'categories' => $categories,
]);