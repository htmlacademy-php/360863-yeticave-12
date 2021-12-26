<?php
require_once('helpers.php');
require_once('functions.php');
require_once('config.php');
require_once('data.php');
require_once('helpers.php');

/* @var mysqli $CONNECTION - ссылка для соединения с базой данных
 * @var int $user_name - переменная имя пользователя
 * @var string $title - переменная title страницы
 * @var array $categories - массив для вывода категорий
 * @var string $searchWord - Поисковой запрос
 */

if (empty($_GET['id'])){
    $content = include_template('404-error.php');
    http_response_code(404);
} else {
    $lot = getLot($CONNECTION);
}

if (empty($lot['id'])){

$content = include_template('404-error.php');
http_response_code(404);

} else  {

$lot = prepareData($lot);

$bids = getbids($CONNECTION);
foreach ($bids as $key => $bid) {
    $bids[$key]['name'] = htmlspecialchars($bid['name']);
    $bids[$key]['sum'] = formatAdPrice(htmlspecialchars($bid['sum']));
    $bids[$key]['time_passed'] =  getTimePassed($bid['date_created_at']);
};

    $content = include_template('lot-item.php', [
    'categories' => $categories,
    'lot' => $lot,
    'bids' => $bids,
]);

}

print include_template('layout.php', [
    'title' => $title,
    'user_name' => $user_name,
    'content' => $content,
    'categories' => $categories,
    'searchWord' => $searchWord,
]);