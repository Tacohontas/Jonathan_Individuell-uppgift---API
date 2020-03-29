<?php
include("../../objects/Products.php");
include("../../objects/Users.php");


// Init errors
$error = false;
$errorMessages = "";

$id = $_POST['id'];
$name = $_POST['name'];
$price = $_POST['price'];
$brand = $_POST['brand'];
$color = $_POST['color'];

// Check for empty values
if (empty($id)) {
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


$user_handler = new User($dbh);
$product_handler = new Product($dbh);

// Check if token belongs to admin
if ($user_handler->checkTokenRole($_POST['token']) == "Admin") {
    // User is admin, check if token is valid

    if ($user_handler->validateToken($_POST['token']) !== false) {
        // Token is valid
        echo $product_handler->updateProduct($id, $name, $price, $brand, $color);
    }
} else {
    // User role isn't admin
    echo "User needs to be Admin.";
}
