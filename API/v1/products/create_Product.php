<?php
include("../../objects/Products.php");
include("../../objects/Users.php");


// Init errors
$error = false;
$errorMessages = "";

// Check for empty values
if (empty($_POST['name'])) {
    $error = true;
    $errorMessages = "Name is empty! ";
}
if (empty($_POST['price'])) {
    $error = true;
    $errorMessages .= "Price is empty! ";
}
if (empty($_POST['brand'])) {
    $error = true;
    $errorMessages .= "Brand is empty! ";
}
if (empty($_POST['color'])) {
    $error = true;
    $errorMessages .= "Color is empty! ";
}

if ($error == true) {
    echo $errorMessages;
    die;
}

$name = $_POST['name'];
$price = $_POST['price'];
$brand = $_POST['brand'];
$color = $_POST['color'];


$user_handler = new User($dbh);
$product_handler = new Product($dbh);

// Check if token belongs to admin
if ($user_handler->checkTokenRole($_POST['token']) == "Admin") {
    // User is admin, check if token is valid

    if ($user_handler->validateToken($_POST['token']) !== false) {
        // Token is valid
        echo $product_handler->createProduct($name, $price, $brand, $color);
    }
} else {
    // User role isn't admin
    echo "User needs to be Admin.";
}
