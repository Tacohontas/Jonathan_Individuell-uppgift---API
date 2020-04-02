<?php
include("../../config/database_handler.php");

class Purchase
{

    private $database_handler;

    public function __construct($database_handler_IN)
    {
        $this->database_handler = $database_handler_IN;
    }

    public function getPurchase($purchaseId_IN)
    {
        /* 
        
        Get purchase by purchase Id.

        If purchase doesnt exist = return false
        If purchase exist        = return purchase

        */
        $query_string = "SELECT Id, Carts_Id, Date_Checkout, Total FROM Purchases WHERE Id = :purchaseId_IN";

        $statementHandler = $this->database_handler->prepare($query_string);
        if ($statementHandler !== false) {
            $statementHandler->bindParam(":purchaseId_IN", $purchaseId_IN);
            $execSuccess = $statementHandler->execute();

            if ($execSuccess === true) {
                $result = $statementHandler->fetch(PDO::FETCH_ASSOC);
                if (!empty($result)) {
                    return $result;
                } else {
                    return false;
                }
            } else {
                $errorMessage = "Execute failed";
                $errorLocation = "getPurchase() in Purchases.php";
            }
        } else {
            $errorMessage = "Execute failed";
            $errorLocation = "getPurchase() in Purchases.php";
        }
        return $this->errorHandler($errorMessage, $errorLocation);
    }

    public function getUsersPurchases($userId_IN)
    {
        /* 
        Get purchases done by User.

        If purchases doesnt exist   = return false
        If purchase/s exist          = return purchases
        */
        $query_string = "SELECT Purchases.Id, Carts_Id , User_Id, Date_Checkout, Total FROM Purchases JOIN Carts on Carts.Id = Carts_Id WHERE User_Id = :userId_IN";

        $statementHandler = $this->database_handler->prepare($query_string);
        if ($statementHandler !== false) {
            $statementHandler->bindParam(":userId_IN", $userId_IN);
            $execSuccess = $statementHandler->execute();

            if ($execSuccess === true) {
                $result = $statementHandler->fetchAll(PDO::FETCH_ASSOC);
                if (!empty($result)) {
                    return $result;
                } else {
                    return false;
                }
            } else {
                $errorMessage = "Execute failed";
                $errorLocation = "getUsersPurchases() in Purchases.php";
            }
        } else {
            $errorMessage = "Execute failed";
            $errorLocation = "getUsersPurchases() in Purchases.php";
        }
        return $this->errorHandler($errorMessage, $errorLocation);
    }

    public function getAllPurchases()
    {

        // Returns all purchases.

        $query_string = "SELECT Id, Carts_Id, Date_Checkout, Total FROM Purchases";

        $statementHandler = $this->database_handler->prepare($query_string);

        if ($statementHandler !== false) {

            $execSuccess = $statementHandler->execute();

            if ($execSuccess === true) {
                $result = $statementHandler->fetchAll(PDO::FETCH_ASSOC);
                if (!empty($result)) {
                    return $result;
                } else {
                    return false;
                }
            } else {
                $errorMessage = "Execute failed";
                $errorLocation = "getAllPurchases() in Purchases.php";
            }
        } else {
            $errorMessage = "Execute failed";
            $errorLocation = "getAllPurchases() in Purchases.php";
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
