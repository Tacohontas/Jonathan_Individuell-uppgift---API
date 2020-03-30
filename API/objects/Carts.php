<?php
include("../../config/database_handler.php");


class Cart
{
    private $database_handler;

    public function __construct($database_handler_IN)
    {
        $this->database_handler = $database_handler_IN;
    }


    public function addToCart($userId_IN, $productId_IN)
    {


        // check if product exist
        if ($this->getProduct($productId_IN) !== false) {
            // Product exists!

            // get cart. (Will create a new one if it doesn't exist).
            $cart = $this->getCart($userId_IN);


            $query_string = "INSERT INTO ProductsInCarts(Carts_Id, Products_Id) VALUES (:cartId , :productId_IN)";
            $statementHandler = $this->database_handler->prepare($query_string);

            if ($statementHandler !== false) {

                $cartId = $cart['Id'];

                $statementHandler->bindParam(":cartId", $cartId);
                $statementHandler->bindParam(":productId_IN", $productId_IN);

                $execSuccess = $statementHandler->execute();

                if ($execSuccess === true) {
                    // Product successfully added to cart, now update total
                    $this->updateTotal($userId_IN);
                    // Return message
                    return "Product added to cart";
                } else {
                    $errorMessage = "Execute failed.";
                    $errorLocation = "addToCart() in Carts.php";
                }
            } else {
                $errorMessage = "Statementhandler failed.";
                $errorLocation = "addToCart() in Carts.php";
            }

            return $this->errorHandler($errorMessage, $errorLocation);
        } else {
            $errorMessage = "Product doesn't exist!";
            $errorLocation = "addToCart() in Carts.php";
            return $this->errorHandler($errorMessage, $errorLocation);
        }
    }

    public function getCart($userId_IN)
    {
        // If cart doesn't exist , create cart
        if ($this->checkCart($userId_IN) === false) {
            // create cart
            // echo "create cart";
            $this->createCart($userId_IN);
            // Run this method again to get cart
            return $this->getCart($userId_IN);
        } else {
            // Cart exists
            // echo "cart exist";
            return $this->checkCart($userId_IN);
        }

        // If cart doesn't exist, create a cart
    }

    public function checkCart($userId_IN)
    {
        /* 

        If cart:
        exists          ->   checkCart() returns cart
        doesn't exist   ->   checkCart() returns FALSE

        */

        $query_string = "SELECT Id, User_Id, Total, Checkout_Done, Date_Created FROM Carts WHERE User_Id = :userId_IN";

        $statementHandler = $this->database_handler->prepare($query_string);

        if ($statementHandler !== false) {

            $statementHandler->bindParam("userId_IN", $userId_IN);

            $execSuccess = $statementHandler->execute();

            if ($execSuccess === true) {

                $result = $statementHandler->fetch(PDO::FETCH_ASSOC);

                if (!empty($result)) {
                    return $result;
                } else {
                    return false;
                }
            } else {
                $errorMessage = "Execute() failed";
                $errorLocation = "checkCart() in Carts.php";
            }
        } else {
            $errorMessage = "Statementhandler failed";
            $errorLocation = "checkCart() in Carts.php";
        }

        return $this->errorHandler($errorMessage, $errorLocation);
    }

    private function createCart($userId_IN)
    {
        $query_string = "INSERT INTO Carts(User_Id) VALUES (:userId_IN)";

        $statementHandler = $this->database_handler->prepare($query_string);

        if ($statementHandler !== false) {

            $statementHandler->bindParam(":userId_IN", $userId_IN);
            $execSuccess = $statementHandler->execute();

            if ($execSuccess === true) {
                // Cart created, return cart_id
                return true;
            } else {
                $errorMessage = "Execute failed.";
                $errorLocation = "createCart() in Carts.php";
            }
        } else {
            $errorMessage = "Statementhandler failed.";
            $errorLocation = "createCart() in Carts.php";
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

    public function getProduct($id_IN)
    {
        /*

        Column = which column to match with value
        Value  = which value match with column

        Example:
        getProduct(Color, "Yellow") will return a product with color yellow. 

        Return FALSE if there's no match in DB

        */

        $query_string = "SELECT Id, Name, Date_Created, Last_Updated, Price, Brand, Color FROM Products WHERE Id = :id_IN ";


        $statementHandler = $this->database_handler->prepare($query_string);

        if ($statementHandler !== false) {

            $statementHandler->bindParam(":id_IN", $id_IN);

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

    public function updateTotal($userId_IN)
    {
        // get total
        $query_string = "SELECT SUM(Price) AS Total FROM Carts JOIN ProductsInCarts ON Carts.Id = Carts_Id JOIN Products ON Products.Id = Products_Id WHERE User_Id = :userId_IN";

        $statementHandler = $this->database_handler->prepare($query_string);

        if ($statementHandler !== false) {
            $statementHandler->bindParam(":userId_IN", $userId_IN);
            $execSuccess = $statementHandler->execute();

            if ($execSuccess === true) {
                $result = $statementHandler->fetch(PDO::FETCH_ASSOC);

                if (!empty($result['Total']) == true) {
                    // Now we have the total. Update carts 'total' column
                    $totalSum = $result['Total'];

                    $query_string = "UPDATE Carts SET Total = :totalSum WHERE User_Id = :userId_IN";
                    $statementHandler = $this->database_handler->prepare($query_string);

                    if ($statementHandler !== false) {
                        $statementHandler->bindParam(":totalSum", $totalSum);
                        $statementHandler->bindParam(":userId_IN", $userId_IN);
                        $execSuccess = $statementHandler->execute();

                        if ($execSuccess === true) {
                            return true;
                        } else {
                            $errorMessage = "Execution failed";
                            $errorLocation = "Update in updateTotal(), Carts.php";
                        }
                    } else {
                        $errorMessage = "Statementhandler failed";
                        $errorLocation = "Update in updateTotal(), Carts.php";
                    }
                }
            } else {
                $errorMessage = "Execution failed";
                $errorLocation = "get total in updateTotal(), Carts.php";
            }
        } else {
            $errorMessage = "Statementhandler failed";
            $errorLocation = "get total in updateTotal(), Carts.php";
        }
        return $this->errorHandler($errorMessage, $errorLocation);
    }
}
