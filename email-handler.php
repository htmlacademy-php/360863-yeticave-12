<?php
require_once('init.php');


/*require 'vendor/autoload.php';*/
/*require_once('data.php');
require_once('functions.php');*/

use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;

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
 * @return array Пустой массив
 */
function handleWinners(mysqli $link, array $winnerLots): array
{
    foreach ($winnerLots as $winner) {
        $winner = getSafeData($winner);
        $winner['userId'] = getLastBidUserId($link, (int)$winner['id']);
        if (empty($winner['userId']['email'])){
            return [];
        }
        include_template('email.php', [
            'winner' => $winner,
        ]);
        $error = sendWinMessage($winner['userId']['email']);

        if (empty($error)) {
            insertWinner($link, $winner['userId']['person_id'],
                $winner['userId']['lot_id']);
        }
    }
    return [];
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
WHERE id = ?";

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