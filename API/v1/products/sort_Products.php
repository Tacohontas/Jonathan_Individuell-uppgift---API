<?php
include("../../objects/Products.php");
include("../../objects/Users.php");

if (empty($_POST['token'])) {
    echo "Need valid token";
    die;
}

// Init errors
$error = false;
$errorMessages = "";

if (empty($_POST['column'])) {
    $error = true;
    $errorMessages .= "Column is empty! ";
}
if (empty($_POST['order'])) {
    $error = true;
    $errorMessages .= "Order is empty! ";
}
if ($error == true) {
    echo $errorMessages;
    die;
}

$user_handler = new User($dbh);
$product_handler = new Product($dbh);

// Check if token is valid

if ($user_handler->validateToken($_POST['token']) !== false) {
    // Token is valid, get sorted result 
    print_r($product_handler->getAllProducts($_POST['column'], $_POST['order']));
}
