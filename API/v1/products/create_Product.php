<?php
include("../../objects/Products.php");
include("../../objects/Users.php");

/*

Create product and add it to DB if:

    - User is admin
    - No field is empty
    - Token is valid

Returns:
    - Confirm message with Product Name on success
    - error messages on failed operations

*/

// Create handlers
$user_handler = new User($dbh);
$product_handler = new Product($dbh);

// Create variables
$name = $_POST['name'];
$price = $_POST['price'];
$brand = $_POST['brand'];
$color = $_POST['color'];

// Init errors
$error = false;
$errorMessages = "";

// Check for empty values
if (empty($token)) {
    $error = true;
    $errorMessages = "Token is empty! ";
}
if (empty($name)) {
    $error = true;
    $errorMessages .= "Name is empty! ";
}
if (empty($price)) {
    $error = true;
    $errorMessages .= "Price is empty! ";
}
if (empty($brand)) {
    $error = true;
    $errorMessages .= "Brand is empty! ";
}
if (empty($color)) {
    $error = true;
    $errorMessages .= "Color is empty! ";
}

if ($error == true) {
    echo $errorMessages;
    die;
}

// Check if token belongs to admin
if ($user_handler->checkTokenRole($token) == "Admin") {
    // User is admin, check if token is valid

    if ($user_handler->validateToken($token) !== false) {
        // Token is valid
        echo $product_handler->createProduct($name, $price, $brand, $color);
    }
} else {
    // User role isn't admin
    echo "User needs to be Admin.";
}
