<?php
include("../../objects/Purchases.php");
include("../../objects/Users.php");


$purchase_handler = new Purchase($dbh);
$user_handler = new User($dbh);

$token = isset($_POST['token']) ? $_POST['token'] : "";
$purchaseId = isset($_POST['purchase_id']) ? $_POST['purchase_id'] : "";

// Init errors
$error = false;
$errorMessages = "";

// Check for empty values
if (empty($token)) {
    $error = true;
    $errorMessages = "token is empty! ";
}

if (empty($purchaseId)) {
    $error = true;
    $errorMessages .= "Purchase Id is empty! ";
}

if ($error == true) {
    echo $errorMessages;
    die;
}


// Check if token belongs to admin
if ($user_handler->checkTokenRole($_POST['token']) == "Admin") {
    // User is admin, check if token is valid
   
    if ($user_handler->validateToken($_POST['token']) !== false) {
        // Token is valid
        $purchase = $purchase_handler->getPurchase($purchaseId);
        if($purchase !== false){
            print_r($purchase);
        } else {
            echo "No purchase found!";
        }
    }
} else {
    // User role isn't admin
    echo "User needs to be Admin.";
}