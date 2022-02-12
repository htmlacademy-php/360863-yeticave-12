<?php
require_once('helpers.php');
require_once('functions.php');
require_once('config.php');
require_once('data.php');
require_once('helpers.php');

/**
 * @var mysqli $CONNECTION - ссылка для соединения с базой данных
 * @var int $userName - переменная имя пользователя
 * @var string $title - переменная title страницы
 * @var array $categories - массив для вывода категорий
 */

$safeData = getSafeData($_REQUEST);
$category = getCategory($CONNECTION, $safeData['category']);

if (empty($category)) {

    $content = include_template('404-error.php', [
        'categories' => $categories,
    ]);

} else {
    $pagination = [
        'pageItems' => 9,
    ];
    $allCategoryAds = getCategoryAdsCount($CONNECTION, $category['symbolic_code']);
    $itemsCount = $allCategoryAds['count'];
    $pageH2 = getCategoryMainHeader($itemsCount, $category);
    $pagination = getPaginationData($itemsCount, $pagination, $safeData);
    $categoryAds = getCategoryAdsForPage(
        $CONNECTION,
        $safeData['category'],
        $pagination['pageItems'],
        $pagination['offset']
    );
    $categoryAds = formatDataAdsCards($categoryAds);

    $content = include_template('category-tmp.php', [
        'categories' => $categories,
        'userName' => $userName,
        'categoryAds' => $categoryAds,
        'pagination' => $pagination,
        'pageH2' => $pageH2,
        'safeData' => $safeData,
    ]);

}
print include_template('layout.php', [
    'title' => $title,
    'userName' => $userName,
    'content' => $content,
    'categories' => $categories,
]);