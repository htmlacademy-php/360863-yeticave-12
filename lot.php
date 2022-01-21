<?php
require_once('helpers.php');
require_once('functions.php');
require_once('config.php');
require_once('data.php');
require_once('helpers.php');
require_once('validate-form.php');

/* @var mysqli $CONNECTION - ссылка для соединения с базой данных
 * @var int $user_name - переменная имя пользователя
 * @var string $title - переменная title страницы
 * @var array $categories - массив для вывода категорий
 */

$safeData = getSafeData($_REQUEST);
if (empty($safeData['id'])){
    $content = include_template('404-error.php');
    http_response_code(404);
} else {
    $lot = getLot($CONNECTION);
}

if (empty($lot['id'])){

$content = include_template('404-error.php', [
    'categories' => $categories,
]);
http_response_code(404);

} else  {

$lot = prepareData($lot);

$bids = getbids($CONNECTION);
$errors = [];
$lotPrice = (int)str_replace(' ', '', $lot['price']);
$bidStep = (int)$lot['bid_step'];

if (isset($_SESSION['user'])) {
    $requiredFields = [
        'cost',
    ];


    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $errors = validateCost($requiredFields, $safeData, $lotPrice, $bidStep);

        if (empty($errors)){
            insertBid($CONNECTION, $safeData['cost'], $_SESSION['user']['id'], $lot['id']);
            $safeData = [];

            header("Location: /lot.php?id=" . $lot['id']);
        }
    }
}


foreach ($bids as $key => $bid) {
    $bids[$key] = getSafeData($bid);
    $bids[$key]['sum'] = formatAdPrice(htmlspecialchars($bid['sum']));
    $bids[$key]['time_passed'] =  getTimePassed($bid['date_created_at']);
};

    $content = include_template('lot-item.php', [
    'categories' => $categories,
    'lot' => $lot,
    'bids' => $bids,
    'user_name' => $user_name,
    'errors' => $errors,
    'safeData' => $safeData,
]);

}

print include_template('layout.php', [
    'title' => $title,
    'user_name' => $user_name,
    'content' => $content,
    'categories' => $categories,
]);