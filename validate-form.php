<?php
/**
 * Получаем массив ошибок после валидации полей формы
 * @param array $requiredFields Массив с полями формы для валидации. Поля для валидации:
 * прикрепление файла image/jpeg', 'image/jpg', 'image/png
 * проверка названия лота
 * проверка сообщения
 * проверка выбранной категории
 * проверка начальной цены
 * проверка шага ставки
 * проверка даты завершения торгов
 * проверка email
 * проверка пароля
 * проверка имени пользователя
 * @param array $safeData Данные, которые заполнили в поля форм
 * @return array Получаем массив ошибок валидации.
 */
function validateForm(array $requiredFields, array $safeData): array
{
    $errors = [];
    if (!empty($_FILES)) {
        $fileName = $_FILES['lot-img']['name'];
        $filePath = __DIR__ . '/uploads/';
        $imgUrlPost = $filePath . $fileName;
        $acceptableImageMime = ['image/jpeg', 'image/jpg', 'image/png'];
    }
    foreach ($requiredFields as $field) {
        if (empty($safeData[$field]) && $safeData[$field] !== '0') {
            switch ($field) {
                case 'lot-name' :
                    $errors[$field] = 'Введите наименование лота';
                    break;
                case 'message' :
                    $errors[$field] = 'Заполните поле';
                    break;
                case 'category' :
                    $errors[$field] = 'Выберите категорию';
                    break;
                case 'lot-rate' :
                    $errors[$field] = 'Введите начальную цену';
                    break;
                case 'lot-step' :
                    $errors[$field] = 'Введите шаг ставки';
                    break;
                case 'lot-date' :
                    $errors[$field] = 'Введите дату завершения торгов';
                    break;
                case 'email' :
                    $errors[$field] = 'Введите ваш email';
                    break;
                case 'password' :
                    $errors[$field] = 'Заполните поле пароль';
                    break;
                case 'name' :
                    $errors[$field] = 'Введите ваше имя';
                    break;
                case 'cost' :
                    $errors[$field] = 'Заполните ставку';
                    break;
            }
        }
    }

    if (!empty($safeData['category'])) {
        if ($safeData['category'] === 'Выберите категорию') {
            $errors['category'] = 'Выберите категорию';
        }
    }

    if (!empty($_FILES)) {
        if ($_FILES['lot-img']["size"] > 0) {
            if (!in_array(mime_content_type($imgUrlPost), $acceptableImageMime)) {
                $errors['lot-img'] = 'Формат изображения может быть только: jpeg, jpg или png';
            }
        }
        if ($_FILES['lot-img']["size"] === 0 && empty($_POST['img'])) {
            $errors['lot-img'] = 'Добавьте изображение для лота';
        }
    }


    if (!empty($safeData['lot-rate'])) {
        if ((filter_var($safeData['lot-rate'], FILTER_VALIDATE_INT) === false) || (int)$safeData['lot-rate'] <= 0) {
            $errors['lot-rate'] = 'Цена должна быть числом больше нуля';
        }
    }


    if (!empty($safeData['lot-step'])) {
        if ($safeData['lot-step'] === '0') {
            if ((filter_var($safeData['lot-step'],
                        FILTER_VALIDATE_INT) === false) || (int)$safeData['lot-step'] <= 0) {
                $errors['lot-step'] = 'Шаг ставки должен быть числом больше нуля';
            }
        }
    }

    if (!empty($safeData['lot-date'])) {

        $date = htmlspecialchars($safeData['lot-date']);
        $dateExplodeArray = explode("-", $date);
        $year = (int)$dateExplodeArray[0];
        $month = (int)$dateExplodeArray[1];
        $day = (int)$dateExplodeArray[2];
        if (!checkdate($month, $day, $year)) {
            $errors['lot-date'] = "Дата должна быть в формате «ГГГГ-ММ-ДД»";
        } else {
            $today = strtotime("now");
            if (strtotime($date) < $today) {
                $errors['lot-date'] = "Дата должна быть больше текущей даты, хотя бы на один день.";
            }
        }
    }

    if (!empty($safeData['email'])) {
        if ((filter_var($safeData['email'], FILTER_VALIDATE_EMAIL,) === false)) {
            $errors['email'] = "введите корректный email";
        }
    }


    return $errors;
}

/**
 * Получаем массив ошибок после валидации полей формы добавления ставки
 * @param array $requiredFields Массив с полями формы для валидации. Поля для валидации:
 * @param array $safeData Данные, которые заполнили в поля формы
 * @param int $lotPrice Стоимость лота из БД
 * @param int $bidStep Шаг ставки из БД
 * @return array Получаем массив ошибок валидации.
 */
function validateCost(array $requiredFields, array $safeData, int $lotPrice, int $bidStep): array
{
    $errors = [];
    foreach ($requiredFields as $field) {
        if (empty($safeData[$field]) && $safeData[$field] !== '0') {
            switch ($field) {
                case 'cost' :
                    $errors[$field] = 'Заполните ставку';
                    break;
            }
        }
    }

    if (!empty($safeData['cost'])) {
        if ($safeData['cost'] !== '0') {
            if ((filter_var($safeData['cost'],
                        FILTER_VALIDATE_INT,) === false) || (int)$safeData['cost'] <= 0 || (int)$safeData['cost'] < ($lotPrice + $bidStep)) {
                $errors['cost'] = 'Ставка должна быть больше или равно, чем текущая цена лота + шаг ставки.';
            }
        }
    }
    return $errors;
}

/**
 * Получаем результат сравнения email, который пользователь ввел. Если такой email есть в базе получаем true если нет false
 * @param mysqli $link Соединение с БД
 * @param string $email Проверяемый email
 * @return bool Булевое значение, есть ли в БД такой email
 */
function compareEmail(mysqli $link, string $email): bool
{
    try {
        $sql_email = "SELECT * FROM person WHERE email= '$email'";
        $object_result_email = mysqli_query($link, $sql_email);
        $foundEmail = mysqli_fetch_assoc($object_result_email);
        if (!empty($foundEmail)) {
            return true;
        }
        if (!$object_result_email) {
            throw new Error ('Ошибка объекта результата MySql:' . ' ' . mysqli_error($link));
        }
        return false;
    } catch (Error $error) {
        return false;
    }
}

/**
 * Получаем результат сравнения пароля и email, который пользователь ввел. Если пароль совпадает с паролем у данного
 * email получаем true если нет false
 * @param mysqli $link Соединение с БД
 * @param string $email Проверяемый email
 * @param string $password Проверяемый пароль
 * @return bool Булевое значение, есть ли в БД такой пароль
 */
function comparePassword(mysqli $link, string $email, string $password): bool
{
    $sql_email = "SELECT * FROM person WHERE email = '$email'";
    $object_result_email = mysqli_query($link, $sql_email);
    $user = $object_result_email ? mysqli_fetch_assoc($object_result_email) : null;
    if ($user) {
        return password_verify($password, $user['password']);
    }
    return false;
}