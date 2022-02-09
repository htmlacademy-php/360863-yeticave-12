<?php
require_once('config.php');
require_once('functions.php');

$title = 'Главная страница';
$userName = null;

$safeUserData = [];
if (!empty($_SESSION['user'])) {
    $safeUserData = getSafeData($_SESSION['user']);
}

if (isset($_SESSION['user']['name'])) {
    $userName = $_SESSION['user']['name'];
}

$searchWord = null;
if (isset($_GET['search'])) {
    $searchWord = htmlspecialchars($_GET['search']);
}

$CONNECTION = mysqli_connect(HOST, USER, PASSWORD, DATABASE);
mysqli_set_charset($CONNECTION, "utf8");
if ($CONNECTION == false) {
    echo "ошибка подключения" . ' ' . mysqli_connect_error();
};

$categories = getCategories($CONNECTION);
if (!empty($categories)) {
    foreach ($categories as $key => $category) {
        $categories[$key] = getSafeData($category);
        $categories[$key]['sectionClass'] = '';
        if (!empty($_POST['category'])) {
            $categories[$key]['sectionClass'] = ($_POST['category'] === $category['id']) ? 'selected' : '';
        }
    }
}

/**
 * Получаем все активные объявления
 * @param mysqli $link Соединение с БД
 * @return array Массив объявлений
 */
function getAds(mysqli $link): array
{
    try {
        $sql_ads = "SELECT lot.id as lotId, lot.title as title, starting_price, completion_date, img, category.title as category, MAX(bid.sum) as current_price
FROM lot
JOIN category ON category.id = lot.category_id
LEFT JOIN bid ON lot.id = bid.lot_id
WHERE completion_date > now()
GROUP BY lot.id, lot.title, starting_price, completion_date, img, lot.date_created_at
ORDER BY lot.date_created_at DESC";

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
function getLot(mysqli $link): array
{
    try {
        $sql_lot = "SELECT lot.id as id, lot.title as title, lot.description as description, starting_price, completion_date, img, category.title as category, MAX(bid.sum) as current_price, bid_step, author_id as authorId
FROM lot
JOIN category ON category.id = lot.category_id
LEFT JOIN bid ON lot.id = bid.lot_id
WHERE lot.id = ?
GROUP BY lot.id, lot.id, lot.title, lot.description, starting_price, completion_date, img, bid_step";
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
        $sql_bids = "SELECT bid.date_created_at, sum, person.name as name
FROM bid
JOIN person ON person_id = person.id
WHERE lot_id = ?
ORDER BY sum DESC";

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
        mysqli_stmt_bind_param($stmt_insert_lot, 'sssssiis', $safeData['lot-name'], $authorID, $safeData['category'],
            $safeData['message'], $imgUrlPost,
            $safeData['lot-rate'], $safeData['lot-step'], $safeData['lot-date']);
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
        $sql_search = "SELECT lot.id as id, lot.title as title, starting_price, completion_date, img, category.title as category, MAX(bid.sum) as current_price, count(bid.id) as bid_sum
FROM lot
JOIN category ON category.id = lot.category_id
LEFT JOIN bid ON lot.id = bid.lot_id
WHERE MATCH (lot.title, lot.description) AGAINST(?)
GROUP BY lot.id, lot.title, starting_price, completion_date, img, lot.date_created_at
ORDER BY lot.date_created_at DESC LIMIT ? OFFSET ?";

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
        $sql_search = "SELECT lot.id as id, lot.title as title, starting_price, completion_date, img, category.title as category, MAX(bid.sum) as current_price, count(bid.id) as bid_sum
FROM lot
JOIN category ON category.id = lot.category_id
LEFT JOIN bid ON lot.id = bid.lot_id
WHERE category.symbolic_code = ? AND lot.completion_date > now()
GROUP BY lot.id, lot.title, starting_price, completion_date, img, lot.date_created_at
ORDER BY lot.date_created_at DESC LIMIT ? OFFSET ?";

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
SELECT MAX(bid.sum) as current_price, bid.lot_id as lotId, bid.person_id, MAX(bid.date_created_at) as bidDate, lot.title as title, lot.completion_date completion_date, category.title as categoryTitle, person.email as email, person.name as name, person.contacts as contacts, lot.img as img
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
LIMIT 1
";

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

/**
 * Получаем все лоты, у которых нет победителя плюс закончилось время
 * @param mysqli $link Соединение с БД
 * @return array Массив с данными лотов
 */
function getWinnerLots(mysqli $link): array
{
    try {
        $sql = "SELECT *
FROM lot
WHERE lot.winner_id is null AND lot.completion_date <= NOW()";
        $object_result = mysqli_query($link, $sql);
        if (!$object_result) {
            throw new Error ('Ошибка объекта результата MySql:' . ' ' . mysqli_error($link));
        }
        return mysqli_fetch_all($object_result, MYSQLI_ASSOC);
    } catch (Error $error) {
        return [];
    }
}

/**
 * Записываем победителя лота в БД
 * @param mysqli $link Соединение с БД
 * @param int $winnerId Id пользователя, ставка которого победила
 * @param int $lotId Id лота, где определился победитель
 */
function insertWinner(mysqli $link, int $winnerId, int $lotId): array
{
    try {

        $sql_update_person = "UPDATE lot 
SET winner_id = ?
WHERE id = ?
";

        $stmt_update_person = mysqli_prepare($link, $sql_update_person);
        if ($stmt_update_person === false) {
            throw new Error('Ошибка подготовленного выражения:' . ' ' . mysqli_error($link));
        }
        mysqli_stmt_bind_param($stmt_update_person, 'ii', $winnerId, $lotId);
        mysqli_stmt_execute($stmt_update_person);
        return [];
    } catch (Error $error) {
        return [];
    }
}

