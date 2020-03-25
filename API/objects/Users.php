<?php
include("../../config/database_handler.php");


class User{
    private $database_handler;
    private $username;
    private $token_validity_time = 15; // Minutes

    public function __construct($datebase_handler_IN)
    {
        $this->database_handler = $datebase_handler_IN;
    }




}