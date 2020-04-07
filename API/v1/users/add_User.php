<?php
include("../../objects/Users.php");

/*
Insert user to DB if:

    - No field is empty
    - Username isnt taken 
    - Email isnt taken

You're also able to set user roles based on DB.
A message will be returned on success.
*/


// Init errors
$error = false;
$errorMessages = "";

// Set variables
$username = isset($_POST['username']) ? $_POST['username'] : "";
$password = isset($_POST['password']) ? $_POST['password'] : "";
$email    = isset($_POST['email'])    ? $_POST['email']    : "";
$roleId   = isset($_POST['role_id'])  ? $_POST['role_id']  : "";

// Check for empty values
if (empty($username)) {
    $error = true;
    $errorMessages = "Username is empty! ";
}

if (empty($password)) {
    $error = true;
    $errorMessages .= "Password is empty! ";
}

if (empty($email)) {
    $error = true;
    $errorMessages .= "Email is empty! ";
}

if (empty($roleId)) {
    $error = true;
    $errorMessages .= "Role_id is empty! ";
}

if ($error == true) {
    echo $errorMessages;
    die;
}


// Add user to DB and return a message.
$user_handler = new User($dbh);

echo $user_handler->addUser($username, $password, $email, $roleId);
