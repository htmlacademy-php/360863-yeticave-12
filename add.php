<?php
require_once('helpers.php');
require_once('data.php');

$categories = getCategories ($CONNECTION);
foreach ($categories as $key => $category) {
    $categories[$key]['title'] = htmlspecialchars($category['title']);
    $categories[$key]['symbolic_code'] = htmlspecialchars($category['symbolic_code']);
};

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

    insertLot($CONNECTION);
    /*    header("Location: /index.php");*/
}


$content = include_template('add-lot.php', [
    'categories' => $categories,
    'imgUrl' => $imgUrl,
    'titlePost' => $titlePost,
    'descriptionPost' => $descriptionPost,
    'startingPricePost' => $startingPricePost,
    'bidStepPost' => $bidStepPost,
    'completionDatePost' => $completionDatePost,
]);

print include_template('layout.php', [
    'title' => $title,
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'content' => $content,
    'categories' => $categories,
]);