<?php
include("../../objects/Users.php");

/*
Login user to DB if:

    - No field is empty
    - User exists in DB

A token will be created and then returned on success.
*/

// Init errors
$error = false;
$errorMessages = "";

// Set variables
$username = isset($_POST['username']) ? $_POST['username'] : "";
$password = isset($_POST['password']) ? $_POST['password'] : "";

// Check for empty values
if (empty($username)) {
    $error = true;
    $errorMessages = "Username is empty! ";
}

if (empty($password)) {
    $error = true;
    $errorMessages .= "Password is empty! ";
}

if($error == true){
    echo $errorMessages;
    die;
}


// Add user to DB and return a token on success.
$user_handler = new User($dbh);
echo $user_handler->loginUser($username, $password);