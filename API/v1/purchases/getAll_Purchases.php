<?php
include("../../objects/Purchases.php");
include("../../objects/Users.php");

$purchase_handler = new Purchase($dbh);
$user_handler = new User($dbh);

$token = isset($_POST['token']) ? $_POST['token'] : "";


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