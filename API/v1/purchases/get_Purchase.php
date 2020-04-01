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

// if (empty($purchaseId)) {
//     $error = true;
//     $errorMessages .= "Purchase Id is empty! ";
// }

if ($error == true) {
    echo $errorMessages;
    die;
}

// Check if user is Admin
// Admins are able to get anyone's purchase by id

if ($user_handler->checkTokenRole($_POST['token']) == "Admin") {
    // User is admin, check if token is valid

    if ($user_handler->validateToken($_POST['token']) !== false) {
        // Token is valid
        $purchase = $purchase_handler->getPurchase($purchaseId);
        if ($purchase !== false) {
            return print_r($purchase);
        } else {
            echo "No purchase found!";
            die;
        }
    }
}

// Regular users can only get their own purchase by id.
// Validate token
if ($user_handler->validateToken($_POST['token']) !== false) {
    // Token is valid, get user_Id
    $userId = $user_handler->getUserFromToken($_POST['token']);
    if (!empty($userId)) {


        if ($user_handler->validateToken($_POST['token']) !== false) {
            // Token is valid

            // Get all of Users purchases to see if purchase_id belong to User.
            $userPurchases = $purchase_handler->getUsersPurchases($userId);
            if ($userPurchases !== false) {
                //$userPurchases will be an array of purchases if user has more than one purchase.
            
                // Check if purchase id belongs to user with a loop.
                $match = false;
                for ($i = 0; $i < count($userPurchases); $i++) {
                    if ($userPurchases[$i]['Id'] == $purchaseId) {
                        $match = true;
                    }
                }

                if ($match == false) {
                    // Purchase dont belong to user
                    echo "No purchase found!";
                    return;
                }

                // Get purchase to return
                return print_r($purchase_handler->getPurchase($purchaseId));
            }
        }
    }
}
