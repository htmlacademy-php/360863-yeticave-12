<?php
require_once('helpers.php');
require_once('functions.php');
require_once('config.php');
require_once('data.php');

/* @var mysqli $CONNECTION - ссылка для соединения с базой данных
 * @var int $userName - переменная имя пользователя
 * @var string $title - переменная title страницы
 * @var array $categories - массив для вывода категорий
 * @var string $searchWord - Поисковой запрос
 */
$safeData = getSafeData($_REQUEST);
if (empty($safeData['search'])) {
    $content = include_template('404-error.php', [
        'categories' => $categories,
    ]);
    http_response_code(404);
} else {
    $pagination = [
        'pageItems' => 9,
    ];
    $safeDataSearch = trim($safeData['search']);
    $allAdsResult = getSearchAdsCount($CONNECTION, $safeDataSearch);
    $itemsCount = $allAdsResult['count'];
    $pagination = getPaginationData($itemsCount, $pagination, $safeData);
    $searchAds = getSearchAdsForPage($CONNECTION, $safeDataSearch, $pagination['pageItems'], $pagination['offset']);
    $searchAds = formatDataAdsCards ($searchAds);
    $searchResult = "Результаты поиска по запросу «{$safeDataSearch}»";
    if (empty($searchAds)) {
        $searchResult = 'Ничего не найдено по вашему запросу';
    }

    $content = include_template('search-tmp.php', [
        'categories' => $categories,
        'safeDataSearch' => $safeDataSearch,
        'pagination' => $pagination,
        'searchResult' => $searchResult,
        'searchAds' => $searchAds,
    ]);
}

print include_template('layout.php', [
    'title' => $title,
    'userName' => $userName,
    'content' => $content,
    'categories' => $categories,
    'searchWord' => $searchWord,

]);


