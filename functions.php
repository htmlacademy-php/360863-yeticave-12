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
function prepareData (array $array): array
{
    foreach ($array as $key => $value){
        $value = htmlspecialchars($value);
        switch ($key){
            case 'bid_step' : $value = formatAdPrice(htmlspecialchars($value)); break;
            case 'completion_date' : $array['timeLeft'] = getTimeLeft(htmlspecialchars($array['completion_date'])); break;
            case 'current_price' : if (!empty($value)) {
                $array['price'] =  formatAdPrice(htmlspecialchars($value), '');
            } else {
                $array['price'] = formatAdPrice(htmlspecialchars($array['starting_price']), '');
            }
                break;
        }
        $array[$key] = $value;
    }
    return $array;
}

