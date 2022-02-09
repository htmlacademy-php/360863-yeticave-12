<?php
require_once('data.php');

/**
 * Форматируем сумму
 * @param string $price Стоимость из БД вида 40100
 * @param string $currency Добавляем валюту, по умолчанию рубли
 * @return string Получаем результат вида 40 100 ₽
 */
function formatAdPrice(string $price, string $currency = ' ₽'): string
{
    $formatedPrice = number_format(ceil($price), 0, ',', ' ');
    return $formatedPrice . $currency;
}

;

/**
 * Получаем сколько осталось времени до окончания торгов
 * @param string $expirationDate Дата завершения торгов из БД
 * @return array Массив [часов осталось, минут осталось]
 */
function getTimeLeft(string $expirationDate): array
{
    $timeNow = date_create(date("Y-m-d H:i"));
    $timeExpiration = date_create($expirationDate);
    $intervalHours = str_pad(+date_interval_format(date_diff($timeNow, $timeExpiration),
            "%a") * 24 + +date_interval_format(date_diff($timeNow, $timeExpiration), "%H"), 2, "0", STR_PAD_LEFT);
    $intervalMinutes = str_pad(date_interval_format(date_diff($timeNow, $timeExpiration), "%i"), 2, "0", STR_PAD_LEFT);
    $timeLeft = [
        'hoursLeft' => $intervalHours,
        'minutesLeft' => $intervalMinutes,
    ];

    return $timeLeft;
}

/**
 * Получаем сколько прошло времени с определенной даты
 * @param string $dateCreate Дата
 * @return string Получаем время с определенной даты. Если прошло меньше минуты запись 'меньше минуты назад';
 * Если прошло больше минуты и меньше часа, то запись вида 'Количество минут назад';
 * Если прошло от часа до двух, то запись вида 'Час назад';
 * Если прошло от часа до 24 часов, то запись вида 'Количество часов назад';
 * Если прошло от 24 часов, то запись вида '22-08-2022 в 22:10';
 */
function getTimePassed(string $dateCreate): string
{
    $timeNow = date_create(date("Y-m-d H:i"));
    $dateCreated = date_create($dateCreate);
    $timePassed = date_diff($dateCreated, $timeNow);
    $days = $timePassed->format('%a');
    $hours = $timePassed->format('%h');
    $minutes = $timePassed->format('%i');
    if ($days == 0 & $hours == 0 & $minutes == 0 ) {
        return 'меньше минуты назад';
    } elseif ($days == 0 & $hours == 0 & $minutes > 0) {
        return $minutes . ' ' . get_noun_plural_form($minutes, 'минута', 'минуты', 'минут') . ' ' . 'назад';
    } elseif ($days == 0 & $hours == 1) {
        return 'Час назад';
    } elseif ($days == 0 & $hours > 1) {
        return $hours . ' ' . get_noun_plural_form($hours, 'час', 'часа', 'часов') . ' ' . 'назад';
    } else {
        return date('d-m-y', strtotime($dateCreate)) . ' ' . 'в' . ' ' . date('H:i', strtotime($dateCreate));
    }
}

/**
 * Получаем безопасные данные из массива
 * @param array $data Массив с данными
 * @return array Массив с безопасными данными.
 * Если в массиве присутствует шаг ставки, то он форматируется в вид "40 000 руб"
 * Если в массиве присутствует дата окончания торгов, то она форматируется в массив [часов осталось, минут осталось]
 * Если в массиве присутствует текущая цена, то он форматируется в вид "40 000 руб"
 * Если в массиве присутствует текущая цена, то он форматируется в вид "40 000"
 */
function prepareData(array $data): array
{
    foreach ($data as $key => $value) {
        $value = htmlspecialchars($value);
        switch ($key) {
            case 'bid_step':
                $value = formatAdPrice(htmlspecialchars($value));
                break;
            case 'completion_date':
                $data['timeLeft'] = getTimeLeft(htmlspecialchars($data['completion_date']));
                break;
            case 'current_price':
                if (!empty($value)) {
                    $data['price'] = formatAdPrice(htmlspecialchars($value), '');
                } else {
                    $data['price'] = formatAdPrice(htmlspecialchars($data['starting_price']), '');
                }
                break;
        }
        $data[$key] = $value;
    }
    return $data;
}

/**
 * Получаем безопасные данные из массива
 * @param array $data Массив с данными
 * @return array Массив с безопасными данными с помощью функции htmlspecialchars.
 */
function getSafeData(array $data): array
{
    $safeData = [];
    foreach ($data as $key => $value) {
        $safeData[$key] = htmlspecialchars($value);
    }

    return $safeData;
}


/**
 * Получаем заголовок для страницы Категории
 * @param mysqli $link Соединение с БД
 * @param array $category Массив со значениями категории
 * @return string Надпись в заголовке H2 для страницы "Категория".
 */
function getCategoryMainHeader(int $items_count, array $category): string
{
    $pageH2 = 'Все лоты в категории "' . $category['title'] . '"';
    if ($items_count == 0) {
        $pageH2 = 'Лоты в категории "' . $category['title'] . '" не найдены';
    }

    return $pageH2;
}


/**
 * Форматирует данные в карточке объявлений для отображения на странице Категории
 * @param array $categoryAds Массив с данными объявлений для выбранной категории
 * @return array Отформатированный массив с данными объявлениями
 */
function formatCategoryAdsCards(array $categoryAds): array
{
    foreach ($categoryAds as $key => $categoryAd) {

        $categoryAds[$key]['starting_price'] = formatAdPrice($categoryAd['starting_price']);

        if (isset($categoryAds[$key]['current_price'])) {
            $categoryAds[$key]['current_price'] = formatAdPrice($categoryAd['current_price']);
        }
        $categoryAds[$key]['timeLeft'] = getTimeLeft($categoryAd['completion_date']);

        $categoryAds[$key]['timerText'] = $categoryAds[$key]['timeLeft']["hoursLeft"] . ':' . $categoryAds[$key]['timeLeft']["minutesLeft"];
        if (strtotime($categoryAds[$key]['completion_date']) <= strtotime('now')) {
            $categoryAds[$key]['timerText'] = 'торги окончены';
        }
    }

    return $categoryAds;
}

/**
 * Получаем данные для пагинации
 * @param int $adsCount Общее количество объявлений
 * @param array $pagination Первоначальные данные пагинации
 * @param array $getData Данные для пагинации из гет запроса
 * @return array Все данные для отображения пагинации
 */
function getPaginationData(int $adsCount, array $pagination, array $getData): array
{
    $pagination['curPage'] = 1;
    if (isset($getData['page'])) {
        $pagination['curPage'] = (int)$getData['page'];
    }

    $pagination['pagesCount'] = ceil($adsCount / $pagination['pageItems']);
    $pagination['offset'] = ($pagination['curPage'] - 1) * $pagination['pageItems'];
    $pagination['pages'] = range(1, $pagination['pagesCount']);

    $pagination['isLastPageExist'] = true;
    if ($pagination['curPage'] == $pagination['pagesCount']) {
        $pagination['isLastPageExist'] = false;
    }

    $pagination['isFirstPageExist'] = true;
    if ($pagination['curPage'] == 1) {
        $pagination['isFirstPageExist'] = false;
    }

    return $pagination;
}

/**
 * Получаем адрес временно загруженного файла
 * @return string Адрес файла
 */
function getFileName(): string
{
    $fileName = $_FILES['lot-img']['name'];
    $filePath = __DIR__ . '/uploads/';
    move_uploaded_file($_FILES['lot-img']['tmp_name'], $filePath . $fileName);
    return $filePath . $fileName;
}

/**
 * Форматирует данные для карточки объявлений
 * @param array $ads Данные для карточек объявлений
 * @return array Отформатированные данные для карточки объявлений
 */
function formatAdsCardsData(array $ads): array
{
    foreach ($ads as $key => $ad) {
        $ads[$key] = getSafeData($ad);
        $ads[$key]['starting_price'] = formatAdPrice($ad['starting_price']);
        $ads[$key]['timeLeft'] = getTimeLeft($ad['completion_date']);
    }

    return $ads;
}
