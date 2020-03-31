<?php
include("../../objects/Carts.php");
include("../../objects/Users.php");

$user_handler = new User($dbh);
$cart_handler = new Cart($dbh);

//init errors
$error = false;
$errorMessage = "";

if (empty($_POST['token'])) {
    $error = true;
    $errorMessage = "Need token. ";
}

if (empty($_POST['id'])) {
    $error = true;
    $errorMessage = "Need Cart id. ";
}

if ($error === true) {
    echo $errorMessage;
    die;
}



// Check token user
if ($user_handler->checkTokenRole($_POST['token']) == "Admin") {
    // User is admin, check if token is valid

    if ($user_handler->validateToken($_POST['token']) !== false) {
        // Token is valid
        if ($cart_handler->deleteCart($_POST['id']) !== false) {
            echo "Cart is now empty.";
            die;
        }
    }
} else {
    // User isnt admin, validate token
    if ($user_handler->validateToken($_POST['token']) !== false) {
        // Token is valid

        // Check if token userId belongs to cart's user id.

        // Get user id from token
        $userId = $user_handler->getUserFromToken($_POST['token']);
        if (!empty($userId)) {
            // check if cart exists
            if ($cart_handler->checkCart($userId) !== false) {
                // cart exist, delete cart
                $cart_handler->deleteCart($_POST['id']);
                echo "Cart is now empty.";
                die;
            };
        }
    }
}

echo "Cart doesn't exist";
die;
