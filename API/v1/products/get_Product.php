<?php
include("../../objects/Products.php");
include("../../objects/Users.php");

/*

Column = which column to match with value
Value  = which value match with column

Example:
getProduct(Color, "Yellow") will return a product with color yellow. 

*/

$match = false;

$column = $_POST['column'];
$value = $_POST['value'];

$allowed_columns = array("Id", "Name", "Date_Created", "Last_Updated", "Price", "Brand", "Color");

// Check if POST['column'] value exist in DB
for($i = 0; $i < count($allowed_columns); $i++){
    if($column == $allowed_columns[$i]){
        $match = true;
    }
}

if($match === false){
    echo "Column doesn't exist";
    die;
}

$user_handler = new User($dbh);
$product_handler = new Product($dbh);

// Check if token is valid

if ($user_handler->validateToken($_POST['token']) !== false) {
    // Token is valid
    if($product_handler->getProduct($column, $value) === false){
        // Product doesnt exist, return message
        echo "Product doesn´t exist";
    } else {
        // Product exists! show result
        print_r($product_handler->getProduct($column, $value));
    }
}



