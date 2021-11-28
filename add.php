<?php
require_once('helpers.php');
require_once('data.php');
require_once('functions.php');
require_once('validate-form.php');

$categories = getCategories ($CONNECTION);
foreach ($categories as $key => $category) {
    $categories[$key]['title'] = htmlspecialchars($category['title']);
    $categories[$key]['symbolic_code'] = htmlspecialchars($category['symbolic_code']);
    $categories[$key]['isSelected'] = ($_POST['category'] === $category['id']) ? 'selected' : '';
};

$requiredFields = ['lot-name', 'category', 'message', 'lot-rate', 'lot-step', 'lot-date'];
$errors = [];
$valuesLotForm = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $valuesLotForm = getValues ($requiredFields);
    $errors = validateForm($requiredFields);
if (count($errors) === 0){
    insertLot($CONNECTION);
    foreach ($valuesLotForm as $key => $value){
        $valuesLotForm[$key] = '';
    }
    foreach ($categories as $key => $category) {
        $categories[$key]['isSelected'] = '';
    };
        header("Location: /index.php");
}
}

$content = include_template('add-lot.php', [
    'categories' => $categories,
    'valuesLotForm' => $valuesLotForm,
    'errors' => $errors,
]);

print include_template('layout.php', [
    'title' => $title,
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'content' => $content,
    'categories' => $categories,
]);