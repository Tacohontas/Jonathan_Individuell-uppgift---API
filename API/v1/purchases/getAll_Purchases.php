<?php
include("../../objects/Purchases.php");
include("../../objects/Users.php");

$purchase_handler = new Purchase($dbh);
$user_handler = new User($dbh);