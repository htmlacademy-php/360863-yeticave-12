<?php

function formatAdPrice(int $price):string
{
    $formatedPrice = number_format(ceil($price), 0, ',', ' ');
    return $formatedPrice . ' ₽';
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


