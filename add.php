<?php
require_once('helpers.php');
require_once('data.php');
require_once('functions.php');
require_once('validate-form.php');

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);



$categories = getCategories ($CONNECTION);
foreach ($categories as $key => $category){
    $categories[$key]['isSelected'] = '';
}
foreach ($categories as $key => $category) {
    $categories[$key]['title'] = htmlspecialchars($category['title']);
    $categories[$key]['symbolic_code'] = htmlspecialchars($category['symbolic_code']);
    if (!empty($_POST['category'])){
        $categories[$key]['isSelected'] = ($_POST['category'] === $category['id']) ? 'selected' : '';
    }

};

$requiredFields = [
    'lot-name',
    'category',
    'message',
    'lot-rate',
    'lot-step',
    'lot-date',
    ];
$errors = [];

$nameValue = '';
$categoryValue = '';
$messageValue = '';
$imgValue = '';
$rateValue = '';
$stepValue = '';
$dateValue = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($_POST['lot-name'])) {
        $nameValue = $_POST['lot-name'];
    }
    if (!empty($_POST['category'])) {
        $categoryValue = htmlspecialchars($_POST['category']);
    }
    if (!empty($_POST['message'])) {
        $messageValue = htmlspecialchars($_POST['message']);
    }
    if (!empty($_POST['lot-rate'])) {
        $rateValue = htmlspecialchars($_POST['lot-rate']);
    }
    if (!empty($_POST['lot-step'])) {
        $stepValue = htmlspecialchars($_POST['lot-step']);
    }
    if (!empty($_POST['lot-date'])) {
        $dateValue = htmlspecialchars($_POST['lot-date']);
    }
    if ($_FILES['lot-img']['size'] > 0) {
        $fileName = $_FILES['lot-img']['name'];
        $filePath = __DIR__ . '/uploads/';
        $imgUrlPost = $filePath . $fileName;
        move_uploaded_file($_FILES['lot-img']['tmp_name'], $filePath . $fileName);
        $imgValue = $filePath . $fileName;
    }

    $errors = validateForm($requiredFields);

if (count($errors) === 0){
    insertLot($CONNECTION);
    $nameValue = '';
    $categoryValue = '';
    $messageValue = '';
    $imgValue = '';
    $rateValue = '';
    $stepValue = '';
    $dateValue = '';
    foreach ($categories as $key => $category) {
        $categories[$key]['isSelected'] = '';
    };
        /*header("Location: /index.php");*/
}
}

$content = include_template('add-lot.php', [
    'categories' => $categories,
    'errors' => $errors,
    'nameValue' => $nameValue,
    'categoryValue' => $categoryValue,
    'messageValue' => $messageValue,
    'imgValue' => $imgValue,
    'rateValue' => $rateValue,
    'stepValue' => $stepValue,
    'dateValue' => $dateValue,

]);

print include_template('layout.php', [
    'title' => $title,
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'content' => $content,
    'categories' => $categories,
]);