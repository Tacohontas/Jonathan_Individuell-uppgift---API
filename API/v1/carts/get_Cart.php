<?php
include("../../objects/Carts.php");
include("../../objects/Users.php");

/*
    Get cart:
    Admin can get any cart. Cart id is required
    Regular users can only get their own cart by token. Not by Cart Id.

    Returns
    - Cart with products and total on success
    - Error messages on failed operations

*/

// Create handlers
$cart_handler = new Cart($dbh);
$user_handler = new User($dbh);

// Create variables
$token  = isset($_POST['token']) ? $_POST['token'] : "";
$cartId = isset($_POST['Id'])    ? $_POST['Id']    : "";

// Init errors
$error = false;
$errorMessages = "";

// Check for empty values
if (empty($token)) {
    $error = true;
    $errorMessages = "token is empty! ";
}
if ($error == true) {
    echo $errorMessages;
    die;
}

// If cart id isnt empty, check for admin rights
if (!empty($cartId)) {
    if ($user_handler->checkTokenRole($token) == "Admin") {
        // User is admin
        // check if token is valid

        if ($user_handler->validateToken($token) == true) {
            // Token is valid

            // Check if Cart id-field is empty
            if (empty($cartId)) {
                echo "Cart Id is empty";
                die;
            }

            // Check if cart exists
            if ($cart_handler->getCartById($cartId) !== false) {
                // Cart exist and is recieved
                print_r($cart_handler->getCartById($cartId));

                // Get user_id from token to get total FIX (måste göras smidigare)
                $userId = $user_handler->getUserFromToken($token);
                if (!empty($userId)) {
                    print_r($cart_handler->getTotal($cartId));
                    print_r($cart_handler->getProductsFromCart($cartId));
                    return;
                }
            } else {
                echo "No active carts found!";
                die;
            }
        }
    } else {
        echo "You're not eligible to search by Cart_Id";
        die;
    }
}

// If cart id is empty, Get cart by token

// Validate token
if ($user_handler->validateToken($token) !== false) {
    // Token is valid, get user_Id
    $userId = $user_handler->getUserFromToken($token);
    if (!empty($userId)) {

        if ($cart_handler->checkCart($userId) !== false) {
            $returnObject = new stdClass;
            // Get cart from checkCart()
            $cart = $cart_handler->checkCart($userId);
            $returnObject->Cart = $cart;
            // Get products in Cart from getProductsInCart()
            $returnObject->Products = $cart_handler->getProductsFromCart($cart['Id']);
            // Get carts total from getTotal()
            $returnObject->Total = $cart_handler->getTotal($cart['Id']);
            // return json
            echo json_encode($returnObject);
            die;
        } else {
            echo "Cart doesn't exist.";
            die;
        }
    }
}
