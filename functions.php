<?php

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
