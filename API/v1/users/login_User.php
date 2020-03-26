<?php
include("../../objects/Users.php");

// test_data
$_POST['username'] = "Test";
$_POST['password'] = "password";


// Init errors
$error = false;
$errorMessages = "";



// Check for empty values
if (empty($_POST['username'])) {
    $error = true;
    $errorMessages = "Username is empty! ";
}

if (empty($_POST['password'])) {
    $error = true;
    $errorMessages .= "Password is empty! ";
}

if($error == true){
    echo $errorMessages;
    die;
}


// Add user to DB and return a message.
$user_handler = new User($dbh);

echo $user_handler->getToken(1, "Admin");

// echo $user_handler->loginUser($_POST['username'], $_POST['password']);
