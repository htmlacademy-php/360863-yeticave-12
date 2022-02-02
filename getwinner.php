<?php

use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;

require 'vendor/autoload.php';
require_once('data.php');
require_once('helpers.php');
require_once('functions.php');
require_once('config.php');

/* @var mysqli $CONNECTION - ссылка для соединения с базой данных
 */

$winnerLots = getWinnerLots($CONNECTION);
if (!empty($winnerLots)) {

    foreach ($winnerLots as $key => $winnerLot) {
        $winnerLot[$key] = getSafeData($winnerLot);
        $winnerLots[$key]['userId'] = getLastBidUserId($CONNECTION, $winnerLot['id']);


        if (!empty($winnerLots[$key]['userId']['email'])) {
            include_template('email.php', [
                'winnerLots[$key]' => $winnerLots[$key],
            ]);
            $dsn = 'smtp://e2ee698ec99225:79fe5e6dc259ff@smtp.mailtrap.io:2525';
            $transport = Transport::fromDsn($dsn);
            $message = new Email();
            $message->to($winnerLots[$key]['userId']['email']);
            $message->from("keks@phpdemo.ru");
            $message->subject("Ваша ставка победила");
            $message->html(fopen('templates/email.php', 'r'));
            $mailer = new Mailer($transport);

            try {
                $mailer->send($message);
            } catch (TransportExceptionInterface $error) {
            }

            if (empty($error)) {
                insertWinner($CONNECTION, $winnerLots[$key]['userId']['person_id'],
                    $winnerLots[$key]['userId']['lot_id']);
            }
        }

    }
}


