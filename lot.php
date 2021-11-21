<?php
require_once('helpers.php');
require_once('functions.php');
require_once('config.php');
require_once('data.php');
require_once('helpers.php');


if ($_GET['id']){
    $lot = getLot($CONNECTION);
}

$categories = getCategories ($CONNECTION);
foreach ($categories as $key => $category) {
    $categories[$key]['title'] = htmlspecialchars($category['title']);
    $categories[$key]['symbolic_code'] = htmlspecialchars($category['symbolic_code']);
};

if (!$_GET['id'] || $lot['lotId'] === null){

$content = include_template('404.php');
http_response_code(404);

} else  {

$lot['title'] = htmlspecialchars($lot['title']);
$lot['category'] = htmlspecialchars($lot['category']);
$lot['description'] = htmlspecialchars($lot['description']);
$lot['bid_step'] = formatAdPrice(htmlspecialchars($lot['bid_step']));
$lot['timeLeft'] = getTimeLeft(htmlspecialchars($lot['completion_date']));
if ($lot['current_price'] !== null) {
    $lotPrice =  formatAdPrice(htmlspecialchars($lot['current_price']), '');
} else {
    $lotPrice = formatAdPrice(htmlspecialchars($lot['starting_price']), '');
}

$bids = getbids($CONNECTION);
foreach ($bids as $key => $bid) {
    $bids[$key]['name'] = htmlspecialchars($bid['name']);
    $bids[$key]['sum'] = formatAdPrice(htmlspecialchars($bid['sum']));
    $bids[$key]['time_passed'] =  getTimePassed($bid['date_created_at']);
};

    $content = include_template('lot-item.php', [
    'categories' => $categories,
    'lot' => $lot,
    'lotPrice' => $lotPrice,
    'bids' => $bids,
]);

}

print include_template('layout.php', [
    'content' => $content,
    'categories' => $categories,
]);