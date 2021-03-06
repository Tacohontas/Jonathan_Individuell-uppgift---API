<?php
include("../../config/database_handler.php");


class Cart
{
    private $database_handler;
    private $cart_validity_time = 2; // in days.

    public function __construct($database_handler_IN)
    {
        $this->database_handler = $database_handler_IN;
    }


    public function addToCart($userId_IN, $productId_IN)
    {
        /* 
        Adds product to cart if:
        Product Exist

        Returns:
        - A confirm message on success
        - Error message/s on fail

        */

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
                    // Product successfully to cart.
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

    public function removeFromCart($userId_IN, $productId_IN)
    {
        /* 
        Removes product from cart if:
        - Cart Exist
        - Product Exists in cart

        Deletes cart if there's no products left after removal.

        Returns:
        - A confirm message on success
        - Error message/s on fail

        */

        // check if product exist
        if ($this->getProduct($productId_IN) !== false) {
            // Product exists!

            // Check if cart exists
            $cart = $this->checkCart($userId_IN);
            if ($cart == false) {
                return "Cart doesn't exist.";
            }

            // Set Cart Id to $cartId
            $cartId = $cart['Id'];

            // Check if product exists in cart
            $result = $this->getProductsFromCart($cartId);

            $match = false;
            for ($i = 0; $i < count($result); $i++) {
                if ($result[$i]['Id'] == $productId_IN) {
                    $match = true;
                }
            }

            if ($match == false) {
                return "Product does not exist in cart";
            }

            $query_string = "DELETE FROM ProductsInCarts WHERE Products_Id = :productId_IN AND Carts_Id = :cartId LIMIT 1";

            $statementHandler = $this->database_handler->prepare($query_string);

            if ($statementHandler !== false) {



                $statementHandler->bindParam(":productId_IN", $productId_IN);
                $statementHandler->bindParam(":cartId", $cartId);

                $execSuccess = $statementHandler->execute();

                if ($execSuccess === true) {
                    // Product successfully removed from cart.

                    // check if cart is empty, if yes = delete.
                    $result = $this->getProductsFromCart($cartId);
                    if (empty($result)) {
                        // Delete cart
                        $this->deleteCart($cartId);
                        return "Last product is removed from cart. Cart is now deleted.";
                    }

                    // Return message
                    return "Product removed from cart";
                } else {
                    $errorMessage = "Execute failed.";
                    $errorLocation = "removeFromCart() in Carts.php";
                }
            } else {
                $errorMessage = "Statementhandler failed";
                $errorLocation = "removeFromCart() in Carts.php";
            }
            return $this->errorHandler($errorMessage, $errorLocation);
        }
    }

    public function getCart($userId_IN)
    {
        /* 

        Get cart whether if it exists or not

        Returns:
        - A new cart if none exist
        - An existing cart if it cart already exist

        */

        // If cart doesn't exist , create cart
        if ($this->checkCart($userId_IN) === false) {
            // create cart
            $this->createCart($userId_IN);
            // Run this method again to get cart
            return $this->getCart($userId_IN);
        } else {
            // Cart exists
            return $this->checkCart($userId_IN);
        }
    }

    public function getAllCarts($status_IN)
    {
        /* 

        Get all carts

        Returns:
        - All carts (even the checkout'ed ones) if  $status_IN = 0
        - Carts that hasn't been check out'ed if    $status_IN = 1

        */

        $query_string = "SELECT Id, User_id, Date_Created, Date_Updated FROM Carts WHERE Checkout_Done = :status_IN";
        $statementHandler = $this->database_handler->prepare($query_string);

        if ($statementHandler !== false) {
            $statementHandler->bindParam(":status_IN", $status_IN);
            $execSuccess = $statementHandler->execute();

            if ($execSuccess === true) {
                $result = $statementHandler->fetchAll(PDO::FETCH_ASSOC);
                if(!empty($result)){
                    return $result;
                } else {
                    return "No carts found.";
                }
            }
        }
    }

    public function checkCart($userId_IN)
    {
        /* 

        Check if cart exists and is valid (updated within 2 days)
        Will only get carts that isnt checkout'ed

        If cart:
        exists and is valid     -> checkCart() returns cart
        exists but isnt valid   -> checkCart() deletes Cart and return false
        doesn't exist           -> checkCart() returns FALSE

        Returns errormessages on failed operations.

        */

        $query_string = "SELECT Id, User_Id, Checkout_Done, Date_Created FROM Carts WHERE User_Id = :userId_IN AND Checkout_Done = 0";

        $statementHandler = $this->database_handler->prepare($query_string);

        if ($statementHandler !== false) {

            $statementHandler->bindParam("userId_IN", $userId_IN);

            $execSuccess = $statementHandler->execute();

            if ($execSuccess === true) {

                $result = $statementHandler->fetch(PDO::FETCH_ASSOC);

                if (!empty($result)) {
                    // Cart exists
                    // check if last updated < 2 days

                    if ($this->validateCart($result['Id']) !== false) {
                        // Cart is valid, return cart
                        return $result;
                    } else {
                        // Cart isnt valid delete cart
                        if ($this->deleteCart($result['Id']) === true) {
                            return false;
                        }
                    }
                } else {
                    // Cart doesn't exist
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
        /* 

        Creates a cart

        Returns:
        - true on success
        - errormessages on failed operations

        */

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

    public function getProduct($id_IN)
    {
        /*
        Get product by product id

        Returns
        - Product if it exists
        - FALSE if there's no match in DB
        
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

    private function validateCart($id_IN)
    {
        /*
        Validate cart if it's been updated the last 2 days.

        Returns
        - TRUE if cart exists and is active
        - Errormessages and FALSE if cart isnt active
        - Error message and FALSE if cart doesn't exist. 

        */

        // Check if cart is active
        $query_string = "SELECT Date_Updated, Id FROM Carts WHERE Id = :id_IN";

        $statementHandler = $this->database_handler->prepare($query_string);
        if ($statementHandler !== false) {

            $statementHandler->bindParam(":id_IN", $id_IN);
            $execSuccess = $statementHandler->execute();

            if ($execSuccess === true) {

                $result = $statementHandler->fetch(PDO::FETCH_ASSOC);

                if (!empty($result)) {
                    // Cart exists
                    $cart_timestamp = new DateTime($result['Date_Updated']);
                    $current_timestamp = new DateTime($this->getCurrentTimeFromDB());

                    // Get interval between Date_Updated and Current time:
                    $interval = $cart_timestamp->diff($current_timestamp);
                    $interval = $interval->format('%d'); // Format result to minutes

                    if ($interval < $this->cart_validity_time) {
                        // Cart is valid, update and return true
                        if ($this->updateCart($id_IN) === true) {
                            return true;
                        }
                    } else {
                        // Cart is not active, return false
                        $errorMessage = "Cart is not active";
                        $this->errorHandler($errorMessage);
                        return false;
                    }
                } else {
                    // Cart doesn't exist, return false
                    $errorMessage = "Cart doesnt exist";
                    $this->errorHandler($errorMessage);
                    return false;
                }
            } else {
                $errorMessage = "Execute Failed";
                $errorLocation = "validateCart() in Carts.php";
            }
        } else {
            $errorMessage = "Statementhandler Failed";
            $errorLocation = "validateCart() in Carts.php";
        }

        return $this->errorHandler($errorMessage, $errorLocation);
    }

    private function updateCart($id_IN)
    {
        /*
        Update cart's Date_Updated-time in DB

        Returns
        - True on success
        - Error messages on failed operations.

        */
        $query_string = "UPDATE Carts SET Date_Updated = CURRENT_TIMESTAMP WHERE (Id = :id_IN)";
        $statementHandler = $this->database_handler->prepare($query_string);

        if ($statementHandler !== false) {

            $statementHandler->bindParam(":id_IN", $id_IN);
            $execSuccess = $statementHandler->execute();

            if ($execSuccess === true) {
                // Cart updated. Return true
                return true;
            } else {
                $errorMessage = "Execute Failed";
                $errorLocation = "updateCart() in Users.php";
            }
        } else {
            $errorMessage = "Statementhandler Failed";
            $errorLocation = "updateCart() in Users.php";
        }

        return $this->errorHandler($errorMessage, $errorLocation);
    }

    public function deleteCart($cartId_IN)
    {
        /* Deletes Cart

        Returns
        - True on success
        - Error messages on failed operations

        */

        $query_string = "DELETE FROM Carts WHERE Id = :cartId_IN";
        $statementHandler = $this->database_handler->prepare($query_string);

        if ($statementHandler !== false) {

            $statementHandler->bindParam(":cartId_IN", $cartId_IN);

            $execSuccess = $statementHandler->execute();

            if ($execSuccess == true) {
                // Cart successfully deleted!
                return true;
            } else {
                $errorMessage = "Execute Failed";
                $errorLocation = "deleteCart() in Carts.php";
            }
        } else {
            $errorMessage = "StatementHandler Failed";
            $errorLocation = "deleteCart() in Carts.php";
        }
        return $this->errorHandler($errorMessage, $errorLocation);
    }

    public function getTotal($cartId_IN)
    {
        /*
        Get total of products in cart by cart id.

        Returns
        - Result on success
        - Error messages on failed operations

        */
        $query_string = "SELECT SUM(Price) AS Total FROM Carts JOIN ProductsInCarts ON Carts.Id = Carts_Id JOIN Products ON Products.Id = Products_Id WHERE Carts_Id = :cartId_IN";

        $statementHandler = $this->database_handler->prepare($query_string);

        if ($statementHandler !== false) {
            $statementHandler->bindParam(":cartId_IN", $cartId_IN);
            $execSuccess = $statementHandler->execute();

            if ($execSuccess === true) {
                $result = $statementHandler->fetch(PDO::FETCH_ASSOC);

                if (!empty($result['Total']) == true) {
                    // Return result
                    return $result['Total'];
                }
            } else {
                $errorMessage = "Execution failed";
                $errorLocation = "getTotal() in Carts.php";
            }
        } else {
            $errorMessage = "Statementhandler failed";
            $errorLocation = "getTotal() in Carts.php";
        }
        return $this->errorHandler($errorMessage, $errorLocation);
    }

    public function getCartById($cartId_IN)
    {
        /* 
        Gets cart by Id (if it exists)

        Returns
        - The cart if cart does exist.
        - FALSE if cart doesn't exist
        - Error messages on failed operations

        */

        $query_string = "SELECT Id, User_Id, Date_Created, Date_Updated FROM Carts WHERE Id = :cartId_IN AND Checkout_Done = FALSE";

        $statementHandler = $this->database_handler->prepare($query_string);

        if ($statementHandler !== false) {

            $statementHandler->bindParam(":cartId_IN", $cartId_IN);
            $execSuccess = $statementHandler->execute();

            if ($execSuccess === true) {
                $result = $statementHandler->fetch(PDO::FETCH_ASSOC);

                if (!empty($result['User_Id']) == true) {
                    // Return result
                    return $result;
                } else {
                    return false;
                }
            } else {
                $errorMessage = "Execution failed";
                $errorLocation = "getCartById() in Carts.php";
            }
        } else {
            $errorMessage = "Statementhandler failed";
            $errorLocation = "getCartById() in Carts.php";
        }
        return $this->errorHandler($errorMessage, $errorLocation);
    }

    public function getProductsFromCart($cartId_IN, $checkout_Done = 0)
    {

        /*
        Get products from carts             if $checkout_Done = 0 (its 0 by default).
        Will get products from checkouts    if $checkout_Done = 1

        Returns
        - Products if cart exist and fits the requested state ($checkout_Done)
        - FALSE if cart doesnt exist or is empty
        - Error messages on failed operations

        */

        $query_string = "SELECT Products.Id, Products.Name, Products.Brand, Products.Price, Products.Color FROM ProductsInCarts JOIN Carts ON Carts.Id = Carts_Id JOIN Products ON Products.Id = Products_Id WHERE Carts_Id = :cartId_IN ";

        if ($checkout_Done > 0) {
            $query_string .= "AND Checkout_Done = TRUE";
        } else {
            $query_string .= "AND Checkout_Done = FALSE";
        }


        $statementHandler = $this->database_handler->prepare($query_string);

        if ($statementHandler !== false) {
            $statementHandler->bindParam(":cartId_IN", $cartId_IN);
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
                $errorLocation = "getProductsFromCart() in Carts.php";
            }
        } else {
            $errorMessage = "Statementhandler failed";
            $errorLocation = "getProductsFromCart() in Carts.php";
        }
        return $this->errorHandler($errorMessage, $errorLocation);
    }

    public function checkoutCart($cartId_IN)
    {
        /* 
        Checkout cart and adds cart to purchase table in DB on success

        Returns 
        - TRUE if cart was successfully added to purchase table
        - Error messages on failed operations

        */
        $query_string = "UPDATE Carts SET Checkout_Done = True WHERE Id = :cartId_IN";

        $statementHandler = $this->database_handler->prepare($query_string);

        if ($statementHandler !== false) {
            $statementHandler->bindParam(":cartId_IN", $cartId_IN);
            $execSuccess = $statementHandler->execute();

            if ($execSuccess === true) {
                // Cart updated. Add to purchase
                if ($this->addToPurchases($cartId_IN) === true) {
                    return true;
                }
            } else {
                $errorMessage = "Execute failed";
                $errorLocation = "checkoutCart() in Carts.php";
            }
        } else {
            $errorMessage = "Statementhandler failed";
            $errorLocation = "checkoutCart() in Carts.php";
        }
        return $this->errorHandler($errorMessage, $errorLocation);
    }

    private function addToPurchases($cartId_IN)
    {
        /* 
        Adds cart to purchase table in DB on success

        Returns 
        - TRUE if cart was successfully added to purchase table
        - Error messages on failed operations

        */

        $query_string = "INSERT INTO Purchases(Carts_Id, Total) VALUES (:cartId_IN, :total)";

        $statementHandler = $this->database_handler->prepare($query_string);

        if ($statementHandler !== false) {
            // Get total sum of products in cart.
            $total = $this->getTotal($cartId_IN);

            $statementHandler->bindParam(":cartId_IN", $cartId_IN);
            $statementHandler->bindParam(":total", $total);

            $execSuccess = $statementHandler->execute();

            if ($execSuccess === true) {
                return true;
            } else {
                $errorMessage = "Execute failed";
                $errorLocation = "addToPurchases() in Carts.php";
            }
        } else {
            $errorMessage = "Statementhandler failed";
            $errorLocation = "addToPurchases() in Carts.php";
        }
        return $this->errorHandler($errorMessage, $errorLocation);
    }

    public function getLastInsertedId()
    {
        // Get last inserted Id to DB
        return $this->database_handler->lastInsertId();
    }

    private function getCurrentTimeFromDB()
    {
        // Get current timestamp from server
        $query_string = "SELECT CURRENT_TIMESTAMP";

        $statementHandler = $this->database_handler->prepare($query_string);
        if ($statementHandler !== false) {
            $execSuccess = $statementHandler->execute();
            if ($execSuccess === true) {
                $result = $statementHandler->fetch(PDO::FETCH_ASSOC);
                return $result['CURRENT_TIMESTAMP'];
            }
        }
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
