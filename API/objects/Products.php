<?php
include("../../config/database_handler.php");

class Product
{
    private $database_handler;

    public function __construct($database_handler_IN)
    {
        $this->database_handler = $database_handler_IN;
    }

    public function createProduct($name_IN, $price_IN, $brand_IN, $color_IN)
    {

 
        // Check if product is already created:
        if ($this->getProduct("Name", $name_IN) === false) {
            // If product doesn't exist: create product and return product name on success

            $query_string = "INSERT INTO Products(Name, Price, Brand, Color) VALUES(:name_IN, :price_IN, :brand_IN, :color_IN)";
            $statementHandler = $this->database_handler->prepare($query_string);

            if ($statementHandler !== false) {

                $statementHandler->bindParam(":name_IN", $name_IN);
                $statementHandler->bindParam(":price_IN", $price_IN);
                $statementHandler->bindParam(":brand_IN", $brand_IN);
                $statementHandler->bindParam(":color_IN", $color_IN);

                $execSuccess = $statementHandler->execute();

                if ($execSuccess === true) {
                    // return Product name
                    return "Product '{$name_IN}' created";
                } else {
                    $errorMessage = "Execute Failed";
                    $errorLocation = "createProduct() in Products.php";
                }
            } else {
                $errorMessage = "StatementHandler Failed";
                $errorLocation = "createProduct() in Products.php";
            }
        } else {
            $errorMessage = "Product already exists";
            return $this->errorHandler($errorMessage);
        }
        return $this->errorHandler($errorMessage, $errorLocation);
    }

    public function getProduct($column_IN, $value_IN)
    {
        // Get product by search terms
        // Return FALSE if there's no match in DB

        $query_string = "SELECT Id, Name, Date_Created, Last_Updated, Price, Brand, Color FROM Products ";

        

        switch ($column_IN) {

            case "Id":
                $query_string .= "WHERE Products.Id = :value_IN ";
                break;
            case "Name":
                $query_string .= "WHERE Name = :value_IN ";
                break;
            case "Date_Created":
                $query_string .= "WHERE Date_Created = :value_IN ";
                break;
            case "Last_Updated":
                $query_string .= "WHERE Last_Updated = :value_IN ";
                break;
            case "Price":
                $query_string .= "WHERE Price = :value_IN ";
                break;
            case "Brand":
                $query_string .= "WHERE Brand = :value_IN ";
                break;
            case "Color":
                $query_string .= "WHERE Color = :value_IN ";
                break;

            default:
                $errorMessage = "Column is not valid";
                $errorLocation = "getProduct() in Users.php";
                return $this->errorHandler($errorMessage, $errorLocation);
        };

        $statementHandler = $this->database_handler->prepare($query_string);
       

        if ($statementHandler !== false) {

            $statementHandler->bindParam(":value_IN", $value_IN);

            $execSuccess = $statementHandler->execute();

            if ($execSuccess === true) {
                // fetch result
                $result = $statementHandler->fetch(PDO::FETCH_ASSOC);

                if (!empty($result)) {
                    // return Product
                    return $result;
                } else {
                    // No match in DB
                    return false;
                }
            } else {
                $errorMessage = "Execute Failed";
                $errorLocation = "createProduct() in Products.php";
            }
        } else {
            $errorMessage = "StatementHandler Failed";
            $errorLocation = "createProduct() in Products.php";
        }
        return $this->errorHandler($errorMessage, $errorLocation);
    }

    private function errorHandler($message_IN, $errorLocation_IN = 0)
    {
        $returnObject = new stdClass;

        $returnObject->message = $message_IN;

        if ($errorLocation_IN !== 0) {
            $returnObject->location = $errorLocation_IN;
        }
        echo json_encode($returnObject);
    }
}
