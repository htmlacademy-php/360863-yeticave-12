<?php
require_once('helpers.php');
require_once('data.php');

$categories = getCategories ($CONNECTION);
foreach ($categories as $key => $category) {
    $categories[$key]['title'] = htmlspecialchars($category['title']);
    $categories[$key]['symbolic_code'] = htmlspecialchars($category['symbolic_code']);
};

$requiredFields = ['lot-name', 'category', 'message', 'lot-rate', 'lot-step', 'lot-date'];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fileName = $_FILES['lot-img']['name'];
    $imgUrlPost = '/uploads/' . $fileName;
    $imgUrl = $imgUrlPost ?? '';
//в инпуте value не сохраняется изображение и категория
    $titlePost = $_POST['lot-name'] ?? '';
    $categoryId = $_POST['category'];
    $descriptionPost = $_POST['message'] ?? '';
    $startingPricePost = $_POST['lot-rate'] ?? '';
    $bidStepPost = $_POST['lot-step'] ?? '';
    $completionDatePost = $_POST['lot-date'] ?? '';



    foreach ($requiredFields as $field){
        if (empty($_POST[$field])){
            switch ($field){
                case 'lot-name' : $errors[$field] = 'Введите наименование лота'; break;
                case 'message' : $errors[$field] = 'Напишите описание лота'; break;
                case 'category' : $errors[$field] = 'Выберите категорию'; break;
                case 'lot-rate' : $errors[$field] = 'Введите начальную цену'; break;
                case 'lot-step' : $errors[$field] = 'Введите шаг ставки'; break;
                case 'lot-date' : $errors[$field] = 'Введите дату завершения торгов'; break;
            }
        }
    }
    if ($_POST['category'] === 'Выберите категорию'){
        $errors['category'] = 'Выберите категорию';
    }
    if ($_FILES['lot-img']["size"] === 0){
        $errors['lot-img'] = 'Добавьте изображение для лота';
    }

    if ($_FILES['lot-img']["size"] > 0){

if (count($errors) > 0){
    insertLot($CONNECTION);
    /*    header("Location: /index.php");*/
}


}


$content = include_template('add-lot.php', [
    'categories' => $categories,
    'imgUrl' => $imgUrl,
    'titlePost' => $titlePost,
    'descriptionPost' => $descriptionPost,
    'startingPricePost' => $startingPricePost,
    'bidStepPost' => $bidStepPost,
    'completionDatePost' => $completionDatePost,
    'errors' => $errors,
]);

print include_template('layout.php', [
    'title' => $title,
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'content' => $content,
    'categories' => $categories,
]);