<?php
include("../../objects/Carts.php");
include("../../objects/Users.php");

/*
    Get all active carts (not the finished ones) if:
        - Token is valid
        - User is admin

    Returns
    - Active carts on success
    - Error messages on failed operations

*/

// Create variables
$token  = isset($_POST['token']) ? $_POST['token'] : "";

// Create handlers
$user_handler = new User($dbh);
$cart_handler = new Cart($dbh);

//Check for empty values
if (empty($token)) {
    echo "Need to input token";
    die;
}




// Check if token belongs to admin
if ($user_handler->checkTokenRole($token) == "Admin") {
    // User is admin, check if token is valid

    if ($user_handler->validateToken($token) !== false) {
        // Token is valid
        print_r($cart_handler->getAllCarts(0));
        die;
    }
} else {
    // User role isn't admin
    echo "User needs to be Admin.";
}
