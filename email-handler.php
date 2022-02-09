<?php

use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;

require 'vendor/autoload.php';
require_once('data.php');
require_once('functions.php');

/**
 * Отправляем сообщение победителям аукциона
 * @param string $email Почта пользователя
 * @return array $error Ошибки отправки
 */
function sendWinMessage(string $email): array
{
    $error = [];
    $dsn = 'smtp://e2ee698ec99225:79fe5e6dc259ff@smtp.mailtrap.io:2525';
    $transport = Transport::fromDsn($dsn);
    $message = new Email();
    $message->to($email);
    $message->from("keks@phpdemo.ru");
    $message->subject("Ваша ставка победила");
    $message->html(fopen('templates/email.php', 'r'));
    $mailer = new Mailer($transport);

    try {
        $mailer->send($message);
    } catch (TransportExceptionInterface $error) {
    }
    return $error;
}

/**
 * Обрабатываем лоты в которых истек срок
 * @param mysqli $link Соединение с БД
 * @param array $winnerLots Массив с лотами, в которых закончилось время и есть победители
 */
function handleWinners(mysqli $link, array $winnerLots): array
{
    foreach ($winnerLots as $key => $winnerLot) {
        $winnerLot[$key] = getSafeData($winnerLot);
        $winnerLots[$key]['userId'] = getLastBidUserId($link, $winnerLot['id']);


        if (!empty($winnerLots[$key]['userId']['email'])) {
            include_template('email.php', [
                'winnerLots[$key]' => $winnerLots[$key],
            ]);
            $error = sendWinMessage($winnerLots[$key]['userId']['email']);

            if (empty($error)) {
                insertWinner($link, $winnerLots[$key]['userId']['person_id'],
                    $winnerLots[$key]['userId']['lot_id']);
            }
        }

    }
    return [];
}