<?php
require_once('init.php');
/*require_once('data.php');*/

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

/**
 * Получаем сколько осталось времени до окончания торгов
 * @param string $expirationDate Дата завершения торгов из БД
 * @return array Массив [часов осталось, минут осталось]
 */
function getTimeLeft(string $expirationDate): array
{
    $timeNow = date_create(date("Y-m-d H:i"));
    $timeExpiration = date_create($expirationDate);
    $intervalHours = str_pad(
        +date_interval_format(date_diff($timeNow, $timeExpiration), "%a") * 24
        + +date_interval_format(date_diff($timeNow, $timeExpiration), "%H"),
        2,
        "0",
        STR_PAD_LEFT
    );
    $intervalMinutes = str_pad(
        date_interval_format(date_diff($timeNow, $timeExpiration), "%i"),
        2,
        "0",
        STR_PAD_LEFT
    );
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
                    $data['price'] = formatAdPrice(htmlspecialchars($data['current_price']), '');
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
function formatDataAdsCards(array $categoryAds): array
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

    $pagination['pagesCount'] = (int)ceil($adsCount / $pagination['pageItems']);
    $pagination['offset'] = ($pagination['curPage'] - 1) * $pagination['pageItems'];
    $pagination['pages'] = range(1, $pagination['pagesCount']);

    $pagination['isLastPageExist'] = true;
    if ($pagination['curPage'] === $pagination['pagesCount']) {
        $pagination['isLastPageExist'] = false;
    }

    $pagination['isFirstPageExist'] = true;
    if ($pagination['curPage'] === 1) {
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

/**
 * Форматирует данные для ставок
 * @param array  $bids Данные для ставок
 * @return array Отформатированные данные для карточки объявлений
 */
function formatBidsData(array $bids): array
{
    foreach ($bids as $key => $bid) {
        $bids[$key] = getSafeData($bid);
        $bids[$key]['sum'] = formatAdPrice(htmlspecialchars($bid['sum']));
        $bids[$key]['time_passed'] = getTimePassed($bid['date_created_at']);
    };

    return $bids;
}


/**
 * Форматирует данные для ставок
 * @param mysqli $link Соединение с БД
 * @param array $userBids Данные ставок
 * @return array Отформатированные данные для карточки объявлений
 */
function formatBetsData(mysqli $link, array $userBids): array
{
    foreach ($userBids as $key => $userBid) {
        $userBids[$key] = prepareData($userBid);
    }

    foreach ($userBids as $key => $userBid) {

        $userBids[$key]['time_passed'] = getTimePassed($userBid['bidDate']);
        $userBids[$key]['lastBidUserId'] = getLastBidUserId($link, (int)$userBid['lotId']);
        if ($userBids[$key]['timeLeft']["hoursLeft"] === '00') {
            $userBids[$key]['timerClass'] = 'timer--finishing';
            $userBids[$key]['timerText'] = $userBid['timeLeft']['hoursLeft'] . ':' . $userBid['timeLeft']['minutesLeft'];
        } elseif (
            strtotime($userBids[$key]['completion_date']) <= strtotime("now")
            && (int)$userBids[$key]['lastBidUserId']['person_id'] == (int)$userBids[$key]['person_id']) {
            $userBids[$key]['timerClass'] = 'timer--win';
            $userBids[$key]['timerText'] = 'Ставка выиграла';
            $userBids[$key]['userContacts'] = $userBid['contacts'];
        } elseif (
            strtotime($userBids[$key]['completion_date']) <= strtotime("now")
            && (int)$userBids[$key]['lastBidUserId']['person_id'] != (int)$userBids[$key]['person_id']) {
            $userBids[$key]['timerClass'] = 'timer--end';
            $userBids[$key]['timerText'] = 'Торги окончены';
        } else {
            $userBids[$key]['timerClass'] = ' ';
            $userBids[$key]['timerText'] = $userBid['timeLeft']['hoursLeft'] . ':' . $userBid['timeLeft']['minutesLeft'];
        }
    }

    return $userBids;
}

/**
 * Получаем все активные объявления
 * @param mysqli $link Соединение с БД
 * @return array Массив объявлений
 */
function getAds(mysqli $link): array
{
    try {
        $sql_ads = "
SELECT 
       lot.id as lotId,
       lot.title as title,
       starting_price,
       completion_date,
       img,
       category.title as category,
       MAX(bid.sum) as current_price
FROM lot
JOIN category ON category.id = lot.category_id
LEFT JOIN bid ON lot.id = bid.lot_id
WHERE completion_date > now()
GROUP BY lot.id, lot.title, starting_price, completion_date, img, lot.date_created_at
ORDER BY lot.date_created_at DESC
";

        $object_result_ads = mysqli_query($link, $sql_ads);
        if (!$object_result_ads) {
            throw new Error ('Ошибка объекта результата MySql:' . ' ' . mysqli_error($link));
        }
        return mysqli_fetch_all($object_result_ads, MYSQLI_ASSOC);
    } catch (Error $error) {
        return [];
    }
}

/**
 * Получаем все категории
 * @param mysqli $link Соединение с БД
 * @return array Массив категорий
 */
function getCategories(mysqli $link): array
{
    try {
        $sql_categories = "SELECT * FROM category";
        $object_result_categories = mysqli_query($link, $sql_categories);
        if (!$object_result_categories) {
            throw new Error ('Ошибка объекта результата MySql:' . ' ' . mysqli_error($link));
        }
        return mysqli_fetch_all($object_result_categories, MYSQLI_ASSOC);
    } catch (Error $error) {
        return [];
    }
}

/**
 * Получаем все данные выбранного лота
 * @param mysqli $link Соединение с БД
 * @return array Массив данных лота
 */
function getLot(mysqli $link, int $id): array
{
    try {
        $sql_lot = "
SELECT 
       lot.id as id,
       lot.title as title,
       lot.description as description,
       starting_price,
       completion_date,
       img,
       category.title as category,
       MAX(bid.sum) as current_price,
       bid_step,
       author_id as authorId
FROM lot
JOIN category ON category.id = lot.category_id
LEFT JOIN bid ON lot.id = bid.lot_id
WHERE lot.id = ?
GROUP BY lot.id, lot.id, lot.title, lot.description, starting_price, completion_date, img, bid_step
";

        $idWithoutChars = preg_replace('/[^0-9]/', '', $id);
        if (strlen($id) !== strlen($idWithoutChars)) {
            throw new Error('id должен содержать только числа');
        }
        settype($id, 'integer');
        if ($id === 0) {
            throw new Error('id должен быть числом');
        }
        $stmt_lot = mysqli_prepare($link, $sql_lot);
        if ($stmt_lot === false) {
            throw new Error('Ошибка подготовленного выражения:' . ' ' . mysqli_error($link));
        }
        mysqli_stmt_bind_param($stmt_lot, 'i', $id);
        mysqli_stmt_execute($stmt_lot);
        $object_result_lot = mysqli_stmt_get_result($stmt_lot);
        return mysqli_fetch_assoc($object_result_lot);
    } catch (Error $error) {
        return [];
    }
}

/**
 * Получаем все ставки выбранного лота
 * @param mysqli $link Соединение с БД
 * @return array Массив данных ставок
 */
function getBids(mysqli $link): array
{
    try {
        $sql_bids = "
SELECT bid.date_created_at, sum, person.name as name
FROM bid
JOIN person ON person_id = person.id
WHERE lot_id = ?
ORDER BY sum DESC
";

        $id = (int)$_GET['id'];
        if (!$id) {
            throw new Error('id должен существовать, а он равен:' . ' ' . $id);
        }
        $idWithoutChars = preg_replace('/[^0-9]/', '', $id);
        if (strlen($id) !== strlen($idWithoutChars)) {
            throw new Error('id должен содержать только числа');
        }
        settype($id, 'integer');
        if ($id === 0) {
            throw new Error('id должен быть числом');
        }
        $stmt_bids = mysqli_prepare($link, $sql_bids);
        if ($stmt_bids === false) {
            throw new Error('Ошибка подготовленного выражения:' . ' ' . mysqli_error($link));
        }
        mysqli_stmt_bind_param($stmt_bids, 'i', $id);
        mysqli_stmt_execute($stmt_bids);
        $object_result_bids = mysqli_stmt_get_result($stmt_bids);
        return mysqli_fetch_all($object_result_bids, MYSQLI_ASSOC);
    } catch (Error $error) {
        return [];
    }
}

/**
 * Добавляем новый лот в БД
 * @param mysqli $link Соединение с БД
 * @param array $safeData Массив данных лота (обязательно: название, id автора, id категории, описание лота, изображение
 * лота, стартовая цена, шаг ставки, дата завершения торгов)
 */
function insertLot(mysqli $link, array $safeData): array
{
    try {
        if (!empty($_FILES)) {
            $file_name = $_FILES['lot-img']['name'];
            $file_path = __DIR__ . '/uploads/';
            $imgUrlPost = '/uploads/' . $file_name;

            move_uploaded_file($_FILES['lot-img']['tmp_name'], $file_path . $file_name);
        }
        if ($_FILES['lot-img']['size'] === 0) {
            $imgUrlPost = $_POST['img'];
        }
        $authorID = $_SESSION['user']['id'];

        $sql_insert_lot = "INSERT INTO lot SET
title = ?,
author_id = ?,
category_id = ?,
description = ?,
img = ?,
starting_price = ?,
bid_step = ?,
completion_date = ?";

        $safeData['lot-rate'] = mysqli_real_escape_string($link, (int)$safeData['lot-rate']);
        $safeData['lot-step'] = mysqli_real_escape_string($link, (int)$safeData['lot-step']);
        $stmt_insert_lot = mysqli_prepare($link, $sql_insert_lot);
        if ($stmt_insert_lot === false) {
            throw new Error('Ошибка подготовленного выражения:' . ' ' . mysqli_error($link));
        }
        mysqli_stmt_bind_param(
            $stmt_insert_lot,
            'sssssiis',
            $safeData['lot-name'],
            $authorID,
            $safeData['category'],
            $safeData['message'],
            $imgUrlPost,
            $safeData['lot-rate'],
            $safeData['lot-step'],
            $safeData['lot-date']);
        mysqli_stmt_execute($stmt_insert_lot);
        return [];
    } catch (Error $error) {
        return [];
    }
}

/**
 * Добавляем нового пользователя в БД
 * @param mysqli $link Соединение с БД
 * @param array $safeData Массив данных пользователя (обязательно: email, имя, пароль, контакты)
 */
function insertPerson(mysqli $link, array $safeData): array
{
    try {
        $safeData['password'] = password_hash($safeData['password'], PASSWORD_BCRYPT);
        $sql_insert_person = "INSERT INTO person SET
email = ?,
name = ?,
password = ?,
contacts = ?";

        $stmt_insert_person = mysqli_prepare($link, $sql_insert_person);
        if ($stmt_insert_person === false) {
            throw new Error('Ошибка подготовленного выражения:' . ' ' . mysqli_error($link));
        }
        mysqli_stmt_bind_param($stmt_insert_person, 'ssss', $safeData['email'], $safeData['name'],
            $safeData['password'], $safeData['message']);
        mysqli_stmt_execute($stmt_insert_person);
        return [];
    } catch (Error $error) {
        return [];
    }
}

/**
 * Получаем массив данных пользователя по его почте
 * @param mysqli $link Соединение с БД
 * @param string $email Email пользователя
 * @return array Массив данных пользователя
 */
function getPersonData(mysqli $link, string $email): array
{
    $sql_email = "SELECT * FROM person WHERE email = '$email'";
    $object_result_email = mysqli_query($link, $sql_email);
    return mysqli_fetch_assoc($object_result_email);
}

/**
 * Получаем количество лотов, которые соответствуют поиску
 * @param mysqli $link Соединение с БД
 * @param string $searchWord Поисковый запрос (должен быть минимум 3 символа)
 * @return array Массив с количеством лотов, которые соответствуют поиску
 */
function getSearchAdsCount(mysqli $link, string $searchWord): array
{
    try {
        $sql_search = "SELECT COUNT(*) AS count
FROM lot
WHERE MATCH (lot.title, lot.description) AGAINST(?)";

        $stmt = mysqli_prepare($link, $sql_search);
        if ($stmt === false) {
            throw new Error('Ошибка подготовленного выражения:' . ' ' . mysqli_error($link));
        }
        mysqli_stmt_bind_param($stmt, 's', $searchWord);
        mysqli_stmt_execute($stmt);
        $object_result = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_assoc($object_result);


    } catch (Error $error) {
        return [];
    }
}

/**
 * Получаем количество лотов, которые соответствуют поиску для страницы с пагинацией
 * @param mysqli $link Соединение с БД
 * @param string $searchWord Поисковый запрос (должен быть минимум 3 символа)
 * @param int $page_items Количество объявлений на странице с пагинацией
 * @param int $offset Смещение относительно начала получаемого списка
 * @return array Массив с данными лотов, которые соответствуют поиску
 */
function getSearchAdsForPage(mysqli $link, string $searchWord, int $page_items, int $offset): array
{
    try {
        $sql_search = "
SELECT
       lot.id as id,
       lot.title as title,
       starting_price,
       completion_date,
       img,
       category.title as category,
       MAX(bid.sum) as current_price,
       count(bid.id) as bid_sum
FROM lot
JOIN category ON category.id = lot.category_id
LEFT JOIN bid ON lot.id = bid.lot_id
WHERE MATCH (lot.title, lot.description) AGAINST(?)
GROUP BY lot.id, lot.title, starting_price, completion_date, img, lot.date_created_at
ORDER BY lot.date_created_at DESC LIMIT ? OFFSET ?
";

        $stmt = mysqli_prepare($link, $sql_search);
        if ($stmt === false) {
            throw new Error('Ошибка подготовленного выражения:' . ' ' . mysqli_error($link));
        }
        mysqli_stmt_bind_param($stmt, 'sii', $searchWord, $page_items, $offset);
        mysqli_stmt_execute($stmt);
        $object_result = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_all($object_result, MYSQLI_ASSOC);

    } catch (Error $error) {
        return [];
    }
}

/**
 * Получаем категорию из адресной строки по символьному коду
 * @param mysqli $link Соединение с БД
 * @param string $categoryId Символьный код категории
 * @return array Массив с данными категории
 */
function getCategory(mysqli $link, string $categoryId): array
{
    try {
        $sql_search = "SELECT *
FROM category
WHERE category.symbolic_code = ?";

        $stmt = mysqli_prepare($link, $sql_search);
        if ($stmt === false) {
            throw new Error('Ошибка подготовленного выражения:' . ' ' . mysqli_error($link));
        }
        mysqli_stmt_bind_param($stmt, 's', $categoryId);
        mysqli_stmt_execute($stmt);
        $object_result = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_assoc($object_result);

    } catch (Error $error) {
        return [];
    }
}


/**
 * Получаем количество активных лотов, которые соответствуют категории по ее символьному коду
 * @param mysqli $link Соединение с БД
 * @param string $category Символьный код категории
 * @return array Массив в котором содержится число количество активных лотов, в категории
 */
function getCategoryAdsCount(mysqli $link, string $category): array
{
    try {
        $sql_search = "SELECT COUNT(*) AS count
FROM lot
JOIN category ON category.id = lot.category_id
WHERE category.symbolic_code = ? AND lot.completion_date > now()";

        $stmt = mysqli_prepare($link, $sql_search);
        if ($stmt === false) {
            throw new Error('Ошибка подготовленного выражения:' . ' ' . mysqli_error($link));
        }
        mysqli_stmt_bind_param($stmt, 's', $category);
        mysqli_stmt_execute($stmt);
        $object_result = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_assoc($object_result);


    } catch (Error $error) {
        return [];
    }
}

/**
 * Получаем количество лотов, которые соответствуют категории по ее символьному коду
 * @param mysqli $link Соединение с БД
 * @param string $category Символьный код категории
 * @param int $page_items Количество объявлений на странице с пагинацией
 * @param int $offset Смещение относительно начала получаемого списка
 * @return array Массив с данными лотов для страницы, которые соответствуют категории
 */
function getCategoryAdsForPage(mysqli $link, string $category, int $page_items, int $offset): array
{
    try {
        $sql_search = "
SELECT 
       lot.id as id,
       lot.title as title,
       starting_price,
       completion_date,
       img,
       category.title as category,
       MAX(bid.sum) as current_price,
       count(bid.id) as bid_sum
FROM lot
JOIN category ON category.id = lot.category_id
LEFT JOIN bid ON lot.id = bid.lot_id
WHERE category.symbolic_code = ? AND lot.completion_date > now()
GROUP BY lot.id, lot.title, starting_price, completion_date, img, lot.date_created_at
ORDER BY lot.date_created_at DESC LIMIT ? OFFSET ?
";

        $stmt = mysqli_prepare($link, $sql_search);
        if ($stmt === false) {
            throw new Error('Ошибка подготовленного выражения:' . ' ' . mysqli_error($link));
        }
        mysqli_stmt_bind_param($stmt, 'sii', $category, $page_items, $offset);
        mysqli_stmt_execute($stmt);
        $object_result = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_all($object_result, MYSQLI_ASSOC);

    } catch (Error $error) {
        return [];
    }
}

/**
 * Записываем ставку пользователя
 * @param mysqli $link Соединение с БД
 * @param int $sum Ставка пользователя
 * @param int $personId Id пользователя
 * @param int $lotId Id лота
 */
function insertBid(mysqli $link, int $sum, int $personId, int $lotId): array
{
    try {
        $sql_insert_bid = "INSERT INTO bid SET
sum = ?,
person_id = ?,
lot_id = ?";

        $stmt_insert_bid = mysqli_prepare($link, $sql_insert_bid);
        if ($stmt_insert_bid === false) {
            throw new Error('Ошибка подготовленного выражения:' . ' ' . mysqli_error($link));
        }
        mysqli_stmt_bind_param($stmt_insert_bid, 'iii', $sum, $personId, $lotId);
        mysqli_stmt_execute($stmt_insert_bid);
        return [];
    } catch (Error $error) {
        return [];
    }
}

/**
 * Получаем все максимальные ставки сделанные пользователем
 * @param mysqli $link Соединение с БД
 * @param int $userId Id пользователя
 * @return array Массив с данными лотов, ставок и данных пользователя
 */
function getUserBids(mysqli $link, int $userId): array
{
    try {
        $sql_bids = "
SELECT 
       MAX(bid.sum) as current_price,
       bid.lot_id as lotId,
       bid.person_id,
       MAX(bid.date_created_at) as bidDate,
       lot.title as title,
       lot.completion_date completion_date,
       category.title as categoryTitle,
       person.email as email,
       person.name as name,
       person.contacts as contacts,
       lot.img as img
FROM lot
JOIN category on category.id = lot.category_id
JOIN person on person.id = lot.author_id
LEFT JOIN bid on bid.lot_id = lot.id
WHERE bid.person_id = ?
GROUP BY bid.lot_id, bid.person_id
";

        $stmt = mysqli_prepare($link, $sql_bids);
        if ($stmt === false) {
            throw new Error('Ошибка подготовленного выражения:' . ' ' . mysqli_error($link));
        }
        mysqli_stmt_bind_param($stmt, 'i', $userId);
        mysqli_stmt_execute($stmt);
        $object_result = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_all($object_result, MYSQLI_ASSOC);

    } catch (Error $error) {
        return [];
    }
}

/**
 * Получаем данные пользователя, который сделал последнюю ставку у лота
 * @param mysqli $link Соединение с БД
 * @param int $lotId Id лота
 * @return array Массив с данными пользователя
 */
function getLastBidUserId(mysqli $link, int $lotId): array
{
    try {
        $sql_bid = "SELECT bid.person_id, bid.lot_id, person.email, person.name
FROM bid
JOIN person on person.id = bid.person_id
WHERE bid.lot_id = ?
ORDER BY bid.date_created_at DESC
LIMIT 1";

        $stmt = mysqli_prepare($link, $sql_bid);
        if ($stmt === false) {
            throw new Error('Ошибка подготовленного выражения:' . ' ' . mysqli_error($link));
        }
        mysqli_stmt_bind_param($stmt, 'i', $lotId);
        mysqli_stmt_execute($stmt);
        $object_result = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_assoc($object_result);


    } catch (Error $error) {
        return [];
    }
}



