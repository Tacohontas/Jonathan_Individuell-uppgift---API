<?php
include("../../objects/Users.php");

// test_data
// $_POST['username'] = "Test";
// $_POST['password'] = "password";
// $_POST['email'] = "test@test.se";
// $_POST['role_id'] = 1;

print_r($_POST);

// Init error
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

if (empty($_POST['email'])) {
    $error = true;
    $errorMessages .= "Email is empty! ";
}

if($error == true){
    echo $errorMessages;
    die;
}


$user_handler = new User($dbh);
echo $user_handler->addUser($_POST['username'], $_POST['password'], $_POST['email'], $_POST['role_id']);