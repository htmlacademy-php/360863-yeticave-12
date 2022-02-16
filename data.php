<?php
require_once('init.php');
/*require_once('config.php');
require_once('functions.php');*/

$title = 'Главная страница';
$userName = null;

$safeUserData = [];
if (!empty($_SESSION['user'])) {
    $safeUserData = getSafeData($_SESSION['user']);
}

if (isset($_SESSION['user']['name'])) {
    $userName = $_SESSION['user']['name'];
}

$searchWord = null;
if (isset($_GET['search'])) {
    $searchWord = htmlspecialchars($_GET['search']);
}

$CONNECTION = mysqli_connect(HOST, USER, PASSWORD, DATABASE);
mysqli_set_charset($CONNECTION, "utf8");
if ($CONNECTION == false) {
    echo "ошибка подключения" . ' ' . mysqli_connect_error();
};

$categories = getCategories($CONNECTION);
if (!empty($categories)) {
    foreach ($categories as $key => $category) {
        $categories[$key] = getSafeData($category);
        $categories[$key]['sectionClass'] = '';
        if (!empty($_POST['category'])) {
            $categories[$key]['sectionClass'] = ($_POST['category'] === $category['id']) ? 'selected' : '';
        }
    }
}

