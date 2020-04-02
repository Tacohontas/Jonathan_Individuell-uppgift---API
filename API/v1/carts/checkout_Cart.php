<?php
/*
    ADD DESC
*/

include("../../objects/Carts.php");
include("../../objects/Users.php");
include("../../objects/Purchases.php");

$cart_handler = new Cart($dbh);
$user_handler = new User($dbh);
$purchase_handler = new Purchase($dbh);

$token = isset($_POST['token']) ? $_POST['token'] : "";
$cartId = isset($_POST['Id']) ? $_POST['Id'] : "";


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


// Validate token
if ($user_handler->validateToken($token) !== false) {
    // Token is valid
    // Get Users id from token to get users cart later.
    $userId = $user_handler->getUserFromToken($token);
    if (!empty($userId)) {
        $cart = $cart_handler->checkCart($userId);
        if (!empty($cart)) {

            //Cart exist, checkout with id from cart
            if($cart_handler->checkoutCart($cart['Id']) === true){
                // return purchase
                $last_inserted_id = $cart_handler->getLastInsertedId();
                $purchase = $purchase_handler->getPurchase($last_inserted_id);
                if($purchase !== false){
                    echo "Checkout done!";
                    print_r($purchase);
                    die;
                }
            }
        } else {
            echo "You have no cart to check out.";
        }
      
    }
}
