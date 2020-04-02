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
        /* 

        Create product if it doesn't exist.

        Returns:
        - product name on success
        - error messages on failed operations

        */


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

                    // Get Id from last inserted value in DB. (Our inserted user)
                    $last_inserted_id = $this->database_handler->lastInsertId();

                    // Fetch user with our id.
                    $return = $this->getProduct("Id", $last_inserted_id);

                    // return Product name
                    return "Product '{$return['Name']}' created";
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

    public function updateProduct($id_IN, $name_IN, $price_IN, $brand_IN, $color_IN)
    {
        /* 

        Update product if product exist

        Returns:
        - a confirm message on success
        - error messages on failed operations

        */


        // Check if product exist:
        if (!empty($this->getProduct("Id", $id_IN))) {
            // product exist

            // init query_string
            $query_string = "";

            if (!empty($name_IN)) {
                $query_string = "UPDATE Products SET Name = :name_IN WHERE Id = :id_IN; ";
            }
            if (!empty($price_IN)) {
                $query_string .= "UPDATE Products SET Price = :price_IN WHERE Id = :id_IN; ";
            }
            if (!empty($brand_IN)) {
                $query_string .= "UPDATE Products SET Brand = :brand_IN WHERE Id = :id_IN; ";
            }
            if (!empty($color_IN)) {
                $query_string .= "UPDATE Products SET Color = :color_IN WHERE Id = :id_IN; ";
            }

            $query_string .= "UPDATE Products SET Last_Updated = CURRENT_TIMESTAMP() WHERE Id = :id_IN; ";

            $statementHandler = $this->database_handler->prepare($query_string);

            if ($statementHandler !== false) {

                if (!empty($name_IN)) {
                    $statementHandler->bindParam(":name_IN", $name_IN);
                }
                if (!empty($price_IN)) {
                    $statementHandler->bindParam(":price_IN", $price_IN);
                }
                if (!empty($brand_IN)) {
                    $statementHandler->bindParam(":brand_IN", $brand_IN);
                }
                if (!empty($color_IN)) {
                    $statementHandler->bindParam(":color_IN", $color_IN);
                }

                $statementHandler->bindParam(":id_IN", $id_IN);

                $execSuccess = $statementHandler->execute();

                if ($execSuccess === true) {

                    // Return a confirm message
                    $message = "Product updated successfully!";
                    return $message;
                } else {
                    $errorMessage = "Execute Failed";
                    $errorLocation = "updateProduct() in Products.php";
                }
            } else {
                $errorMessage = "StatementHandler Failed";
                $errorLocation = "updateProduct() in Products.php";
            }
        } else {
            $errorMessage = "Product doesn't exists";
            return $this->errorHandler($errorMessage);
        }
        return $this->errorHandler($errorMessage, $errorLocation);
    }

    public function getProduct($column_IN, $value_IN)
    {
        /*
        Get product from DB based on a combination of column and value

        $column_IN = which column to match with value
        $value_IN  = which value to match with column

        Example:
        getProduct(Color, "Yellow") will return a product with color yellow. 

        Returns
        - Product on success
        - FALSE if there's no match in DB
        - Error messages on failed operations

        */

        $query_string = "SELECT Id, Name, Date_Created, Last_Updated, Price, Brand, Color FROM Products ";

        switch ($column_IN) {

            case "Id":
                $query_string .= "WHERE Id = :value_IN ";
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
                $errorLocation = "getProduct() in Products.php";
            }
        } else {
            $errorMessage = "StatementHandler Failed";
            $errorLocation = "getProduct() in Products.php";
        }
        return $this->errorHandler($errorMessage, $errorLocation);
    }

    public function getAllProducts($column_IN = 0, $order_IN = 0)
    {

        /*
        Get all products!
        + sort by column if $column_IN & $order_IN is used.

        Returns
        - Result on success
        - Error message on failed operations or faulty inputs.

        */

        $query_string = "SELECT Id, Name, Date_Created, Last_Updated, Price, Brand, Color FROM Products ";

        // Set Column from $column_IN if its not 0 (its 0 by default)
        if ($column_IN !== 0) {
            switch ($column_IN) {

                case "Name":
                    $query_string .= "ORDER BY Name ";
                    break;

                case "Price":
                    $query_string .= "ORDER BY Price ";
                    break;

                case "Brand":
                    $query_string .= "ORDER BY Brand ";
                    break;

                case "Color":
                    $query_string .= "ORDER BY Color ";
                    break;
                case "Date_Created":
                    $query_string .= "ORDER BY Date_Created ";
                    break;
                case "Last_Updated":
                    $query_string .= "ORDER BY Last_Updated ";
                    break;
                default:
                    $errorMessage = "Column is not valid";
                    $errorLocation = "getAllProducts() in Products.php";
                    return $this->errorHandler($errorMessage, $errorLocation);
            };
        }

        if ($order_IN !== 0) {
            $query_string .= $order_IN;
        };

        $statementHandler = $this->database_handler->prepare($query_string);

        if ($statementHandler !== false) {


            $execSuccess = $statementHandler->execute();

            if ($execSuccess === true) {
                // Fetch result
                $result = $statementHandler->fetchAll(PDO::FETCH_ASSOC);

                // return result
                return $result;
            } else {
                $errorMessage = "Execute Failed";
                $errorLocation = "getAllProducts() in Products.php";
            }
        } else {
            $errorMessage = "StatementHandler Failed";
            $errorLocation = "getAllProducts() in Products.php";
        }
        return $this->errorHandler($errorMessage, $errorLocation);
    }

    public function deleteProduct($id_IN)
    {
        /*
        Delete product if it exists

        Returns
        - TRUE on Success
        - FALSE on failed execute
        - Error messages on other failed operations

        */


        // check if file exists
        if ($this->getProduct("Id", $id_IN) !== false) {

            $query_string = "DELETE FROM Products WHERE Id = :id_IN";
            $statementHandler = $this->database_handler->prepare($query_string);

            if ($statementHandler !== false) {

                $statementHandler->bindParam(":id_IN", $id_IN);
                $execSuccess = $statementHandler->execute();

                if ($execSuccess == true) {
                    // Deleted product successfully
                    return true;
                } else {
                    // Execute failed
                    return false;
                }
            } else {
                $errorMessage = "StatementHandler Failed";
                $errorLocation = "deleteProduct() in Products.php";
            }
        } else {
            $errorMessage = "Product doesn't exist.";
            $errorLocation = "deleteProduct() in Products.php";
        }
        return $this->errorHandler($errorMessage, $errorLocation);
    }

    private function errorHandler($message_IN, $errorLocation_IN = 0)
    {
        // Return error messages in json format

        $returnObject = new stdClass;

        $returnObject->message = $message_IN;

        if ($errorLocation_IN !== 0) {
            $returnObject->location = $errorLocation_IN;
        }
        echo json_encode($returnObject);
    }
}
