<?php

function validateForm ($requiredFields){
    $errors = [];
    $fileName = $_FILES['lot-img']['name'];
    $filePath = __DIR__ . '/uploads/';
    $imgUrlPost = $filePath . $fileName;
    $acceptableImageMime = ['image/jpeg', 'image/jpg', 'image/png'];

    foreach ($requiredFields as $field){
        if (empty($_POST[$field]) && $_POST[$field] !== '0'){
            switch ($field){
                case 'lot-name' : $errors[$field] = 'Введите наименование лота'; break;
                case 'message' : $errors[$field] = 'Напишите описание лота'; break;
                case 'category' : $errors[$field] = 'Выберите категорию'; break;
                case 'lot-rate' : $errors[$field] = 'Введите начальную цену'; break;
                case 'lot-step' : $errors[$field] = 'Введите шаг ставки'; break;
                case 'lot-date' : $errors[$field] = 'Введите дату завершения торгов'; break;
                default: $errors[$field] = 'Заполните поле';
            }
        }
    }

//валидация поля Категория
    if ($_POST['category'] === 'Выберите категорию'){
        $errors['category'] = 'Выберите категорию';
    }
//валидация поля Изображение
    if ($_FILES['lot-img']["size"] === 0){
        $errors['lot-img'] = 'Добавьте изображение для лота';
    } else {
        if (!in_array(mime_content_type($imgUrlPost), $acceptableImageMime)){
            $errors['lot-img'] = 'Формат изображения может быть только: jpeg, jpg или png';
        }
    }

//валидация поля Начальная цена
    if (!empty($_POST['lot-rate']) || $_POST['lot-rate'] === '0') {
        preg_match("@^([1-9][0-9]*)$@", $_POST['lot-rate'], $price_match);
        if(!$price_match[0]){
            $errors['lot-rate'] = 'Цена должна быть числом больше нуля';
        }
    }

//валидация поля Шаг ставки
    if (!empty($_POST['lot-step']) || $_POST['lot-step'] === '0') {
        preg_match("@^([1-9][0-9]*)$@", $_POST['lot-step'], $price_match);
        if(!$price_match[0]){
            $errors['lot-step'] = 'Шаг ставки должен быть числом больше нуля';
        }
    }

//валидация поля Дата
    if (!empty($_POST['lot-date'])){

        $date = $_POST['lot-date'];
        $array = explode("-",$date);
        $year = (int)$array[0];
        $month = (int)$array[1];
        $day = (int)$array[2];
        if(!checkdate($month, $day, $year))
        {
            $errors['lot-date'] = "Дата должна быть в формате «ГГГГ-ММ-ДД»";
        }
        else
        {
            $today = strtotime("now");
            if(strtotime($date)<$today)
                $errors['lot-date'] = "Дата должна быть больше текущей даты, хотя бы на один день.";
        }
    }
    return $errors;
}