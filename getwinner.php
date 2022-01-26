<?php
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
require 'vendor/autoload.php';
require_once('data.php');

/* @var mysqli $CONNECTION - ссылка для соединения с базой данных

 */

$winnerLots = getWinnerLots($CONNECTION);
if (!empty($winnerLots)){

    foreach ($winnerLots as $key => $winnerLot){
        $winnerLot[$key] = getSafeData($winnerLot);
        $winnerLots[$key]['userId'] = getLastBidUserId ($CONNECTION, $winnerLot['id']);

        if(!empty($winnerLots[$key]['userId']['email'])){
            // Конфигурация траспорта
            $dsn = 'smtp://e2ee698ec99225:79fe5e6dc259ff@smtp.mailtrap.io:2525';
            $transport = Transport::fromDsn($dsn);
// Формирование сообщения
            $message = new Email();
            $message->to($winnerLots[$key]['userId']['email']);
            $message->from("keks@phpdemo.ru");
            $message->subject("Ваша ставка победила");
            $message->text("Вашу гифку «Кот и пылесос» посмотрело больше 1 млн!");
// Отправка сообщения
            $mailer = new Mailer($transport);

            try {
                $mailer->send($message);
            } catch (TransportExceptionInterface $error) {
                // некая ошибка предотвратила отправку письма; отобразить сообщение
                // об ошибке или попробовать отправить сообщение повторно
                print ($error);
            }

            if (empty($error)){
                insertWinner ($CONNECTION, $winnerLots[$key]['userId']['person_id'], $winnerLots[$key]['userId']['lot_id']);
            }
        }

    }
}

