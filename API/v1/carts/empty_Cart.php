<?php
include("../../objects/Carts.php");
include("../../objects/Users.php");

/*

Empties the cart by cartId if you're admin, or your own cart if you're not admin.

Requirements:
    - Token is valid
    - Cart exists

Returns 
    - confirm message on success
    - Error message/s on failed operations

*/

// Create handlers
$user_handler = new User($dbh);
$cart_handler = new Cart($dbh);

//init errors
$error = false;
$errorMessage = "";

// Create variables
$token = isset($_POST['token']) ? $_POST['token'] : "";
$cartId = isset($_POST['Id']) ? $_POST['Id'] : "";


if (empty($token)) {
    $error = true;
    $errorMessage = "Need token. ";
}
if ($error === true) {
    echo $errorMessage;
    die;
}



// If cart id isnt empty, check token for admin rights
if (!empty($cartId)) {
    if ($user_handler->checkTokenRole($token) == "Admin") {
        // User is admin, check if token is valid

        if ($user_handler->validateToken($token) !== false) {
            // Token is valid
            if ($cart_handler->deleteCart($cartId) !== false) {
                echo "Cart is now empty.";
                die;
            }
        }
    } else {
        echo "You're not eligible to search by Cart_Id. You can only get your own cart by token.";
        die;
    }
}

// User isnt admin, validate token
if ($user_handler->validateToken($token) !== false) {
    // Token is valid

    // Check if token userId belongs to cart's user id.

    // Get user id from token
    $userId = $user_handler->getUserFromToken($token);
    if (!empty($userId)) {
        // check if cart exists
        $cart = $cart_handler->checkCart($userId);
        if ($cart !== false) {
            // cart exist, delete cart
            $cart_handler->deleteCart($cart['Id']);
            echo "Cart is now empty.";
            die;
        };
    }
}


echo "Cart doesn't exist";
die;
