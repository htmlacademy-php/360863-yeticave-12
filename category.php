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
 */

$safeData = getSafeData($_REQUEST);

$category = getCategory($CONNECTION, $safeData['category']);

if (isset($safeData['page'])) {
    $cur_page = (int)$safeData['page'];
} else {
    $cur_page = 1;
}
$page_items = 9;
$allCategoryAds = getCategoryAdsCount($CONNECTION, $safeData['category']);
$items_count = $allCategoryAds['count'];
if ($items_count == 0) {
    $pageH2 = 'Лоты в категории "' . $category['title'] . '" не найдены';
} else {
    $pageH2 = 'Все лоты в категории "' . $category['title'] . '"';
}


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

$categoryAds = getCategoryAdsForPage($CONNECTION, $safeData['category'], $page_items, $offset);

foreach ($categoryAds as $key => $categoryAd) {
    $categoryAds[$key]['starting_price'] = formatAdPrice($categoryAd['starting_price']);
    if (isset($categoryAds[$key]['current_price'])) {
        $categoryAds[$key]['current_price'] = formatAdPrice($categoryAd['current_price']);
    }
    $categoryAds[$key]['timeLeft'] = getTimeLeft($categoryAd['completion_date']);
    if (strtotime($categoryAds[$key]['completion_date']) <= strtotime('now')) {
        $categoryAds[$key]['timerText'] = 'торги окончены';
    } else {
        $categoryAds[$key]['timerText'] = $categoryAds[$key]['timeLeft']["hoursLeft"] . ':' . $categoryAds[$key]['timeLeft']["minutesLeft"];
    }

}

$content = include_template('category-tmp.php', [
    'categories' => $categories,
    'category' => $category,
    'user_name' => $user_name,
    'categoryAds' => $categoryAds,
    'pages_count' => $pages_count,
    'pages' => $pages,
    'cur_page' => $cur_page,
    'isFirstPageExist' => $isFirstPageExist,
    'isLastPageExist' => $isLastPageExist,
    'pageH2' => $pageH2,
    'safeData' => $safeData,
]);


print include_template('layout.php', [
    'title' => $title,
    'user_name' => $user_name,
    'content' => $content,
    'categories' => $categories,
]);