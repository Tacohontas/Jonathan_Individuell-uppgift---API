<?php
include("../../objects/Purchases.php");
include("../../objects/Users.php");

/* 

    Get all the purchases
    Users can only get their own purchases
    Admins can get any purchase by puchase id.

    - Need valid token
    - No empty values is allowed


    Returns
    - All purchases on success
    - FALSE if there is none
    - Error messages on failed operations

*/

// Create handlers
$purchase_handler = new Purchase($dbh);
$user_handler = new User($dbh);

// Create variables
$token = isset($_POST['token']) ? $_POST['token'] : "";

// Check for empty values
if(empty($token)){
    echo "Need to input token";
    die;
}

// Check if token belongs to admin
if ($user_handler->checkTokenRole($token) == "Admin") {
    // User is admin, check if token is valid

    if ($user_handler->validateToken($token) !== false) {
        // Token is valid
        print_r($purchase_handler->getAllPurchases());
        die;
    }
} else {
    // User role isn't admin
    echo "User needs to be Admin.";
}