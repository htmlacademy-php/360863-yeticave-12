<?php
require_once('helpers.php');
require_once('functions.php');
require_once('config.php');
require_once('data.php');

/* @var mysqli $CONNECTION - ссылка для соединения с базой данных
 * @var int $user_name - переменная имя пользователя
 * @var string $title - переменная title страницы
 * @var array $categories - массив для вывода категорий
 */

if (empty($_GET['search'])) {
    $content = include_template('404-error.php');
    http_response_code(404);
} else {
    $safeDataSearch = trim(htmlspecialchars($_GET['search']));
    $searchAds = getSearchAds($CONNECTION, $safeDataSearch);

    foreach ($searchAds as $searchAd) {
        $searchAd['starting_price'] = formatAdPrice($searchAd['starting_price']);
        var_dump($searchAd['starting_price']);
        $searchAd['timeLeft'] = getTimeLeft($searchAd['completion_date']);
    }

print_r('<br>');
var_dump($searchAds);

    if(empty($searchAds)) {
        $searchResult = 'Ничего не найдено по вашему запросу';
    } else {
        $searchResult = "Результаты поиска по запросу «{$safeDataSearch}»";
    }




    $content = include_template('search-tmp.php', [
        'safeDataSearch' => $safeDataSearch,
        'searchResult' => $searchResult,
        'searchAds' => $searchAds,
    ]);
}

print include_template('layout.php', [
    'title' => $title,
    'user_name' => $user_name,
    'content' => $content,
    'categories' => $categories,

]);


