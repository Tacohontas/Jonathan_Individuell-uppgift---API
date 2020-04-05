<?php
include("../../objects/Carts.php");
include("../../objects/Users.php");

/*

Add product to user's shopping cart if:

    - No field is empty
    - Token is valid

Will get A confirm message on success
Error message/s on failed operations

*/


// Create handlers
$cart_handler = new Cart($dbh);
$user_handler = new User($dbh);

// Create variables
$token     = isset($_POST['token']) ? $_POST['token'] : "";
$productId = isset($_POST['Id'])    ? $_POST['Id']    : "";



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



