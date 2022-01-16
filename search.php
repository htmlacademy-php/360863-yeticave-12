<?php
require_once('helpers.php');
require_once('functions.php');
require_once('config.php');
require_once('data.php');

/* @var mysqli $CONNECTION - ссылка для соединения с базой данных
 * @var int $user_name - переменная имя пользователя
 * @var string $title - переменная title страницы
 * @var array $categories - массив для вывода категорий
* @var string $searchWord - Поисковой запрос
 */
$safeData = getSafeData($_REQUEST);
if (empty($safeData['search']) ) {
    $content = include_template('404-error.php');
    http_response_code(404);
} else {
    $safeDataSearch = trim($safeData['search']);

    if(isset($safeData['page'])){
        $cur_page = (int) $safeData['page'];
    } else {
        $cur_page = 1;
    }
    $page_items = 9;

    $allAdsResult = getSearchAds($CONNECTION, $safeDataSearch);

    $items_count = count($allAdsResult);

    $pages_count = ceil($items_count / $page_items);
    $offset = ($cur_page - 1) * $page_items;
    $pages = range(1, $pages_count);

    if ($cur_page == $pages_count) {
        $isLastPageExist = false;
    } else {
        $isLastPageExist = true;
    }

    if ($cur_page == 1) {
        $isFirstPageExist = false;
    } else {
        $isFirstPageExist = true;
    }

    $searchAds = getSearchAdsForPage($CONNECTION, $safeDataSearch, $page_items, $offset);

    foreach ($searchAds as $key => $searchAd) {
        $searchAds[$key]['starting_price'] = formatAdPrice($searchAd['starting_price']);
        if(isset($searchAds[$key]['current_price'])){
            $searchAds[$key]['current_price'] = formatAdPrice($searchAd['current_price']);
        }
        $searchAds[$key]['timeLeft'] = getTimeLeft($searchAd['completion_date']);
    }

    if(empty($searchAds)) {
        $searchResult = 'Ничего не найдено по вашему запросу';
    } else {
        $searchResult = "Результаты поиска по запросу «{$safeDataSearch}»";
    }


    if (isset($safeData['page']) && $safeData['page'] > $pages_count){
        $content = include_template('404-error.php', [
            'categories' => $categories,
        ]);
        http_response_code(404);
    } else {
        $content = include_template('search-tmp.php', [
            'categories' => $categories,
            'safeDataSearch' => $safeDataSearch,
            'searchResult' => $searchResult,
            'searchAds' => $searchAds,
            'pages_count' => $pages_count,
            'pages' => $pages,
            'cur_page' => $cur_page,
            'isFirstPageExist' => $isFirstPageExist,
            'isLastPageExist' => $isLastPageExist,
        ]);
    }

}

print include_template('layout.php', [
    'title' => $title,
    'user_name' => $user_name,
    'content' => $content,
    'categories' => $categories,
    'searchWord' => $searchWord,

]);


