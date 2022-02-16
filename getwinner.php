<?php
require_once('init.php');

/* @var mysqli $CONNECTION - ссылка для соединения с базой данных
 */

$winnerLots = getWinnerLots($CONNECTION);

if (!empty($winnerLots)) {
    handleWinners($CONNECTION, $winnerLots);
}
