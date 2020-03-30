<?php
include("../../objects/Products.php");
include("../../objects/Users.php");


// Init errors
$error = false;
$errorMessages = "";

// Check for empty values
if (empty($_POST['Id'])) {
    echo "Id is empty!";
    die;
}


$user_handler = new User($dbh);
$product_handler = new Product($dbh);

// Check if token belongs to admin
if ($user_handler->checkTokenRole($_POST['token']) == "Admin") {
    // User is admin, check if token is valid

    if ($user_handler->validateToken($_POST['token']) == true) {
        // Token is valid
        if($product_handler->deleteProduct($_POST['Id']) == true){
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