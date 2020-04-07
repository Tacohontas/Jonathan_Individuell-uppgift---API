<?php
include("../../objects/Products.php");
include("../../objects/Users.php");

/*

Get product/s from DB based on a combination of column and value

Column = which column to match with value
Value  = which value match with column

Example:
getProduct(Color, "Yellow") will return a product/s with color yellow. 

*/
// Create handlers
$user_handler = new User($dbh);
$product_handler = new Product($dbh);

// Create variables
$token = isset($_POST['token']) ? $_POST['token'] : "";
$column = isset($_POST['column']) ? $_POST['column'] : "";
$value = isset($_POST['value']) ? $_POST['value'] : "";

// Check for empty values
$error = false;
$errorMessages = "";

if (empty($token)) {
    echo "Token is empty!";
    die;
}
if (empty($column)) {
    echo "Id is empty!";
    die;
}
if (empty($value)) {
    echo "Id is empty!";
    die;
}
if ($error == true) {
    echo $errorMessages;
    die;
}


// Init column match
$match = false;

// Enter searchable columns from DB here:
$allowed_columns = array(
    "Id",
    "Name",
    "Date_Created",
    "Last_Updated",
    "Price",
    "Brand",
    "Color"
);

// Check if POST['column'] value exist in DB
for ($i = 0; $i < count($allowed_columns); $i++) {
    if ($column == $allowed_columns[$i]) {
        $match = true;
    }
}

if ($match === false) {
    echo "Column doesn't exist";
    die;
}


// Check if token is valid
if ($user_handler->validateToken($token) !== false) {
    // Token is valid
    if ($product_handler->getProduct($column, $value) === false) {
        // Product doesnt exist, return message
        echo "Product doesn´t exist";
    } else {
        // Product exists! show result
        print_r($product_handler->getProduct($column, $value));
    }
}
