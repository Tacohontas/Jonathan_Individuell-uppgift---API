<?php
include("../../objects/Carts.php");
include("../../objects/Users.php");

if(empty($_POST['token'])){
    echo "Need to input token";
    die;
}


$user_handler = new User($dbh);
$cart_handler = new Cart($dbh);

// Check if token belongs to admin
if ($user_handler->checkTokenRole($_POST['token']) == "Admin") {
    // User is admin, check if token is valid

    if ($user_handler->validateToken($_POST['token']) !== false) {
        // Token is valid
        print_r($cart_handler->getAllCarts(0));
        die;
    }
} else {
    // User role isn't admin
    echo "User needs to be Admin.";
}