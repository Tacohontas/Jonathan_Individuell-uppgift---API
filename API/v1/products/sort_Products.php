<?php
include("../../objects/Products.php");
include("../../objects/Users.php");



// Input variables
$token = isset($_POST['token']) ? $_POST['token'] : "";
$column = isset($_POST['column']) ? $_POST['column'] : "";
$order = isset($_POST['order']) ? $_POST['order'] : "";
$limit = isset($_POST['limit']) ? $_POST['limit'] : "";
$offset = isset($_POST['offset']) ? $_POST['offset'] : "";



// Init errors
$error = false;
$errorMessages = "";


// Check for empty values
// Offset doesn't need a value, it will be set to 0 if it's empty.

if (empty($token)) {
    $error = true;
    $errorMessages .= "Token is empty! ";
}
if (empty($column)) {
    $error = true;
    $errorMessages .= "Column is empty! ";
}
if (empty($order)) {
    $error = true;
    $errorMessages .= "Order is empty! ";
}
if (empty($limit)) {
    $error = true;
    $errorMessages .= "limit is empty! ";
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
    // Set Limit and offset based on input
    print_r($product_handler->getAllProducts($limit, $offset, $column, $order));
}
