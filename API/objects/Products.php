<?php
include("../../config/database_handler.php");

class Products
{
    private $database_handler;

    public function __construct($database_handler_IN)
    {
        $this->database_handler = $database_handler_IN;
    }

    
}
