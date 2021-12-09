<?php

function formatAdPrice(string $price, string $currency = ' ₽'):string
{
    $formatedPrice = number_format(ceil($price), 0, ',', ' ');
    return $formatedPrice . $currency;
};

function getTimeLeft (string $expirationDate):array
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

function getTimePassed (string $dateCreate):string
{
    $timeNow = date_create(date("Y-m-d H:i"));
    $dateCreated = date_create($dateCreate);
    $timePassed = date_diff($dateCreated, $timeNow);
    $days = $timePassed -> format('%a');
    $hours = $timePassed -> format('%h');
    $minutes = $timePassed -> format('%i');
    if ($days == 0 & $hours == 0 & $minutes == 0 ){
        return $timePassed = 'меньше минуты назад';
    } else if ($days == 0 & $hours == 0 & $minutes > 0){
        return $timePassed = $minutes . ' ' . get_noun_plural_form($minutes, 'минута', 'минуты', 'минут') . ' ' . 'назад';
    } else if ($days == 0 & $hours == 1){
        return $timePassed = 'Час назад';
    } else if ($days == 0 & $hours > 1){
        return $timePassed = $hours . ' ' . get_noun_plural_form($hours, 'час', 'часа', 'часов') . ' ' . 'назад';
    } else {
        return $timePassed = date('d-m-y', strtotime($dateCreate)) . ' ' . 'в' . ' ' . date('H:i', strtotime($dateCreate));
    }
}
function prepareData (array $data): array
{
    foreach ($data as $key => $value){
        $value = htmlspecialchars($value);
        switch ($key){
            case 'bid_step':
                $value = formatAdPrice(htmlspecialchars($value));
                break;
            case 'completion_date':
                $data['timeLeft'] = getTimeLeft(htmlspecialchars($data['completion_date']));
                break;
            case 'current_price':
                if (!empty($value)) {
                    $data['price'] =  formatAdPrice(htmlspecialchars($value), '');
            } else {
                    $data['price'] = formatAdPrice(htmlspecialchars($data['starting_price']), '');
            }
                break;
        }
        $data[$key] = $value;
    }
    return $data;
}

function getSafeData (array $data, mysqli $link): array
{
    $safeData = [];
    foreach ($data as $key => $value) {
        $safeData[$key] = mysqli_real_escape_string($link, $value);
        $safeData[$key] = htmlspecialchars($value);
    }

    return $safeData;
}