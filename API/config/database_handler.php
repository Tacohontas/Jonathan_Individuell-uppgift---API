<?php

// PHP settings
$host = "localhost";
$user = "root";
$pass = "";
$db = "PHP_Jonis_individuella_1";


// Try (MAKE CONNECTION)
try {
    $dsn = "mysql:host=$host;dbname=$db;";
    $dbh = new PDO($dsn, $user, $pass, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));

    // ON ERROR
} catch (PDOException $e) {
    // Hämtar felmeddelande från PDO
    echo "Error!" . $e->getMessage() . "<br>";
    die;
}
