<?php
include("../../objects/Carts.php");
include("../../objects/Users.php");




// Create handlers
$cart_handler = new Cart($dbh);
$user_handler = new User($dbh);


$token = isset($_POST['token']) ? $_POST['token'] : "";
$productId = isset($_POST['Id']) ? $_POST['Id'] : "";



// Init errors
$error = false;
$errorMessages = "";

// Check for empty values
if (empty($token)) {
    $error = true;
    $errorMessages = "token is empty! ";
}

if (empty($productId)) {
    $error = true;
    $errorMessages .= "Product Id is empty! ";
}

if ($error == true) {
    echo $errorMessages;
    die;
}



if ($user_handler->validateToken($token) !== false) {
    // Token is valid
    $userId = $user_handler->getUserFromToken($token);
    if (!empty($userId)) {
        echo $cart_handler->addToCart($userId, $productId);
        return;
    }
}



