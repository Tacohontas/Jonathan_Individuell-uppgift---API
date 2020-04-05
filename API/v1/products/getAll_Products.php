<?php
include("../../objects/Products.php");
include("../../objects/Users.php");


/*
    Get all products!
    You need to set an limit and offset for pagination causes.

    Returns
    - Result on success
    - Error message on failed operations or faulty inputs.

*/


// Input variables
$token = isset($_POST['token']) ? $_POST['token'] : "";
$limit = isset($_POST['limit']) ? $_POST['limit'] : "";
$offset = isset($_POST['offset']) ? $_POST['offset'] : "";

// Init errors
$error = false;
$errorMessages = "";

// Check for empty fields.
// Offset doesn't need a value, it will be set to 0 if it's empty.

if (empty($token)) {
    $error = true;
    $errorMessages .= "Token is empty! ";
}
if (empty($limit)) {
    $error = true;
    $errorMessages .= "limit is empty! ";
}
if ($error == true) {
    echo $errorMessages;
    die;
}

// Create handlers
$user_handler = new User($dbh);
$product_handler = new Product($dbh);

// Check if token is valid

if ($user_handler->validateToken($token) !== false) {
    // Token is valid, print result
    print_r($product_handler->getAllProducts($limit, $offset));
}
