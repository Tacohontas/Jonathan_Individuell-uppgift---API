<?php
include("../../objects/Carts.php");
include("../../objects/Users.php");

/*
    Admin can get any cart. Cart id is required
    Other users can only get their own cart by token.
*/


$cart_handler = new Cart($dbh);
$user_handler = new User($dbh);

$token = isset($_POST['token']) ? $_POST['token'] : "";



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
if (!empty($_POST['Id'])) {
    if ($user_handler->checkTokenRole($_POST['token']) == "Admin") {
        // User is admin
        // check if token is valid

        if ($user_handler->validateToken($_POST['token']) == true) {
            // Token is valid

            // Check if Cart id-field is empty
            if (empty($_POST['Id'])) {
                echo "Cart Id is empty";
                die;
            }

            // Check if cart exists
            if ($cart_handler->getCartById($_POST['Id']) !== false) {
                // Cart exist and is recieved
                print_r($cart_handler->getCartById($_POST['Id']));

                // Get user_id from token to get total FIX (måste göras smidigare)
                $userId = $user_handler->getUserFromToken($_POST['token']);
                if (!empty($userId)) {
                    print_r($cart_handler->getTotal($userId));
                    print_r($cart_handler->getProductsFromCart($userId));
                    return;
                }
            } else {
                echo "Cart doesn't exist.";
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
if ($user_handler->validateToken($_POST['token']) !== false) {
    // Token is valid, get user_Id
    $userId = $user_handler->getUserFromToken($_POST['token']);
    if (!empty($userId)) {
       
        if ($cart_handler->checkCart($userId) !== false) {
            $returnObject = new stdClass;
            // Get cart from checkCart()
            $returnObject->Cart = $cart_handler->checkCart($userId);
            // Get products in Cart from getProductsInCart()
            $returnObject->Products = $cart_handler->getProductsFromCart($userId);
            // Get carts total from getTotal()
            $returnObject->Total = $cart_handler->getTotal($userId);
            // return json
            echo json_encode($returnObject);
            die;
        } else {
            echo "Cart doesn't exist.";
            die;
        }
    }
}
