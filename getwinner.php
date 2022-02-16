<?php

require_once('data.php');
require_once('functions.php');
require_once('email-handler.php');

/* @var mysqli $CONNECTION - ссылка для соединения с базой данных
 */

$winnerLots = getWinnerLots($CONNECTION);

if (!empty($winnerLots)) {
    handleWinners($CONNECTION, $winnerLots);
}
