<?php
include("../../objects/Products.php");
include("../../objects/Users.php");

 /* 

    Update product if:
    - product exist
    - User is admin
    - Token is valid

    Returns:
    - a confirm message on success
    - error messages on failed operations

*/

// Init errors
$error = false;
$errorMessages = "";

// Create variables
$token     = isset($_POST['token']) ? $_POST['token'] : "";
$productId = isset($_POST['id'])    ? $_POST['id']    : "";
$name      = isset($_POST['name'])  ? $_POST['name']  : "";
$price     = isset($_POST['price']) ? $_POST['price'] : "";
$brand     = isset($_POST['brand']) ? $_POST['brand'] : "";
$color     = isset($_POST['color']) ? $_POST['color'] : "";


// Check for empty values
if (empty($token)) {
    $error = true;
    $errorMessages = "token is empty! ";
}
if (empty($productId)) {
    $error = true;
    $errorMessages = "Id is empty! ";
}
if (empty($name) && empty($price) && empty($brand) && empty($color)) {
    $error = true;
    $errorMessages = "Choose column to update.";
}
if ($error == true) {
    echo $errorMessages;
    die;
}

// Create handlers
$user_handler = new User($dbh);
$product_handler = new Product($dbh);

// Check if token belongs to admin
if ($user_handler->checkTokenRole($token) == "Admin") {
    // User is admin, check if token is valid

    if ($user_handler->validateToken($token) !== false) {
        // Token is valid
        echo $product_handler->updateProduct($productId, $name, $price, $brand, $color);
    }
} else {
    // User role isn't admin
    echo "User needs to be Admin.";
}
