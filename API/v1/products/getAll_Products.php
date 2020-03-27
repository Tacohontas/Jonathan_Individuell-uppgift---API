<?php
include("../../objects/Products.php");
include("../../objects/Users.php");

if (empty($_POST['token'])) {
    echo "Need token";
    die;
}

$user_handler = new User($dbh);
$product_handler = new Product($dbh);

// Check if token is valid

if ($user_handler->validateToken($_POST['token']) !== false) {
    // Token is valid, print result
    print_r($product_handler->getAllProducts());
}
