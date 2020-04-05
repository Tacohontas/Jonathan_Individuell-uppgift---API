<?php
include("../../objects/Products.php");
include("../../objects/Users.php");

/*

Deletes product from DB (and from shopping carts) if:

    - User is admin
    - No field is empty
    - Token is valid
    - Product exists

Returns:
    - Confirm message on success
    - Error message/s on failed operations

*/

// Create handlers
$user_handler = new User($dbh);
$product_handler = new Product($dbh);

// Create variables
$token = isset($_POST['token']) ? $_POST['token'] : "";
$productId = isset($_POST['Id']) ? $_POST['Id'] : "";


// Init errors
$error = false;
$errorMessages = "";

// Check for empty values
if (empty($token)) {
    echo "Token is empty!";
    die;
}
if (empty($productId)) {
    echo "Id is empty!";
    die;
}
if ($error == true) {
    echo $errorMessages;
    die;
}


// Check if token belongs to admin
if ($user_handler->checkTokenRole($token) == "Admin") {
    // User is admin, check if token is valid

    if ($user_handler->validateToken($token) == true) {
        // Token is valid
        if($product_handler->deleteProduct($productId) == true){
            // Product was deleted
            echo "Product was deleted!";
            die;
        } else {
            echo "Product wasn't deleted";
            die;
        }
    }
} else {
    // User role isn't admin
    echo "User needs to be Admin.";
}