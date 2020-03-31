<?php
include("../../objects/Carts.php");

$user_handler = new Cart($dbh);

echo $user_handler->deleteCart(1);
