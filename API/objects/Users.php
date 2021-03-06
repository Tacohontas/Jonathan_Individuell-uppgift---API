<?php
include("../../config/database_handler.php");


class User
{
    private $database_handler;
    private $token_validity_time = 15; // Validity time in minutes

    public function __construct($datebase_handler_IN)
    {
        $this->database_handler = $datebase_handler_IN;
    }


    public function addUser($username_IN, $password_IN, $email_IN, $roleId_IN)
    {
        /*
        Add user to DB through insertUserToDB-method if username & email is unique

        Returns 
        - Confirm message from insertUserToDb() on success
        - Error messages on failed operations

        */

        if ($this->isUsernameTaken($username_IN) === false) {

            if ($this->isEmailTaken($email_IN) === false) {

                // Try to insert User to DB
                $result = $this->insertUserToDB($username_IN, $password_IN, $email_IN, $roleId_IN);

                if ($result !== false) {
                    return $result;
                } else {
                    $errorMessage = "Couldn't insert User in DB";
                }
            } else {
                $errorMessage = "Email is taken";
            }
        } else {
            $errorMessage = "Username is taken";
        }



        $errorLocation = "AddUser in Users.php";
        return $this->errorHandler($errorMessage, $errorLocation);
    }

    private function isUsernameTaken($username_IN)
    {
        /*
        Checks if username is taken.

        Returns 
        - TRUE if its unique
        - FALSE if username exist in DB already
        - Error messages on failed operations

        */

        $query_string = "SELECT Username FROM Users WHERE Username = :username_IN";

        $statementHandler = $this->database_handler->prepare($query_string);

        if ($statementHandler !== false) {

            $statementHandler->bindParam(":username_IN", $username_IN);
            $execSuccess = $statementHandler->execute();

            if ($execSuccess === true) {

                $result = $statementHandler->fetch(PDO::FETCH_ASSOC);

                if (!empty($result)) {
                    return true;
                } else {
                    return false;
                }
            } else {
                $errorMessage = "Execute failed";
                $errorLocation = "isUsernameTaken in Users.php";
            }
        } else {
            $errorMessage = "Statementhandler failed";
            $errorLocation = "isUsernameTaken in Users.php";
        }

        return $this->errorHandler($errorMessage, $errorLocation);
    }

    private function isEmailTaken($email_IN)
    {
        /*
        Checks if email is taken.

        Returns 
        - TRUE if its unique
        - FALSE if email exist in DB already
        - Error messages on failed operations

        */

        $query_string = "SELECT Email FROM Users WHERE Email = :email_IN";

        $statementHandler = $this->database_handler->prepare($query_string);

        if ($statementHandler !== false) {

            $statementHandler->bindParam(":email_IN", $email_IN);
            $execSuccess = $statementHandler->execute();

            if ($execSuccess === true) {
                $result = $statementHandler->fetch(PDO::FETCH_ASSOC);

                if (!empty($result)) {
                    return true;
                } else {
                    return false;
                }
            } else {
                $errorMessage = "Execute failed";
                $errorLocation = "isEmailTaken in Users.php";
            }
        } else {
            $errorMessage = "Statementhandler failed";
            $errorLocation = "isEmailTaken in Users.php";
        }

        return $this->errorHandler($errorMessage, $errorLocation);
    }

    private function insertUserToDb($username_IN, $password_IN, $email_IN, $roleId_IN)
    {
        /*
        Inserts user to DB.
        Returns 
        - Confirm message on success
        - Error messages on failed operations

        */
        $query_string = "INSERT INTO Users(Username, Password, Email, Roles_Id) VALUES(:username_IN , :password_IN, :email_IN, :roleId_IN)";

        $statementHandler = $this->database_handler->prepare($query_string);

        if ($statementHandler !== false) {

            $encryptedPassword = md5($password_IN);

            $statementHandler->bindParam(":username_IN", $username_IN);
            $statementHandler->bindParam(":password_IN", $encryptedPassword);
            $statementHandler->bindParam(":email_IN", $email_IN);
            $statementHandler->bindParam(":roleId_IN", $roleId_IN);

            $execSuccess = $statementHandler->execute();

            if ($execSuccess === true) {

                // Get Id from last inserted value in DB. (Our inserted user)
                $last_inserted_id = $this->database_handler->lastInsertId();

                // Fetch user with our id.
                $return = $this->getUser("Id", $last_inserted_id);

                // Return a confirm message
                $message = "{$return['Role']} '{$return['Username']}' inserted to DB successfully";
                return $message;
            } else {
                $errorMessage = "Execute Failed.";
                $errorLocation = "insertUserToDb() in Users.php";
            }
        } else {
            $errorMessage = "Statementhandler Failed.";
            $errorLocation = "insertUserToDb() in Users.php";
        }

        // Return Errormessages
        return $this->errorHandler($errorMessage, $errorLocation);
    }

    public function getUser($column, $value, $column2 = false, $value2 = false)
    {

        /*
        Get User from DB based on combination/s of column and value

        $column_IN = which column to match with value
        $value_IN  = which value to match with column

        Example:
        getUser(Username, "Janne Ball") will return a User with username "Janne Ball". 

        You can also use two SQL-WHERE clauses, hence $column and $column2 etc.

        Returns
        - User on success
        - FALSE if there's no match in DB
        - FALSE and Error messages on failed operations

        */

        // Init twoColumns
        $twoColumns = false;

        $query_string = "SELECT Users.Id, Username, Email, Date_Created, Name AS Role FROM Users JOIN Roles ON Roles_Id = Roles.Id ";

        // Set Column from string
        switch ($column) {

            case "Id":
                $query_string .= "WHERE Users.Id = :value ";
                break;

            case "Username":
                $query_string .= "WHERE Username = :value ";
                break;

            case "Email":
                $query_string .= "WHERE Email = :value ";
                break;

            case "Date_Created":
                $query_string .= "WHERE Date_Created = :value ";
                break;
            default:
                $errorMessage = "First column is not valid";
                $errorLocation = "getUser() in Users.php";
                return $this->errorHandler($errorMessage, $errorLocation);
        };

        // If we have two columns to match:
        if ($column2 !== false && $value2 !== false) {
            $twoColumns = true;
            switch ($column2) {

                case "Id":
                    $query_string .= "AND Users.Id = :value2";
                    break;

                case "Username":
                    $query_string .= "AND Username = :value2";
                    break;

                case "Password":
                    $query_string .= "AND Password = :value2";
                    break;

                case "Email":
                    $query_string .= "AND Email = :value2";
                    break;

                case "Date_Created":
                    $query_string .= "AND Date_Created = :value2";
                    break;
                default:
                    $errorMessage = "Second column is not valid";
                    $errorLocation = "getUser() in Users.php";
                    return $this->errorHandler($errorMessage, $errorLocation);
            };
        }

        $statementHandler = $this->database_handler->prepare($query_string);
        if ($statementHandler !== false) {

            $statementHandler->bindParam(":value", $value);

            if ($twoColumns == true) { // If we have two columns to match:
                $statementHandler->bindParam(":value2", $value2);
            }

            $execSuccess = $statementHandler->execute();

            if ($execSuccess === true) {

                $result = $statementHandler->fetch(PDO::FETCH_ASSOC);

                // If our result isn't empty = we've got a match!
                if (!empty($result)) {
                    return $result;
                } else {
                    // No match in DB
                    return false;
                }
            } else {
                $errorMessage = "Execute failed";
                $errorLocation = "getUser() in Users.php";
            }
        } else {
            $errorMessage = "Statementhandler failed";
            $errorLocation = "getUser() in Users.php";
        }

        echo $this->errorHandler($errorMessage, $errorLocation);
        return false;
    }

    public function loginUser($username_IN, $password_IN)
    {
        /*
        Login user, and create token.

        Returns
        - Token on success
        - Error message if something went wrong

        */

        $encryptedPassword = md5($password_IN);
        $return = $this->getUser("Username", $username_IN, "Password", $encryptedPassword);
        if ($return == true) {
            // Login success
            // Return token through getToken()
            return $this->getToken($return['Id'], $return['Username']);
        } else {
            $errorMessage = "Invalid username/password.";
            return $this->errorHandler($errorMessage);
        }
    }


    private function createToken($userId_IN, $username_IN)
    {
        /* Create a unique token

        Returns 
        - Token on Success
        - Error messages on failed operations

        */
        $uniqueToken = md5($username_IN . uniqid('', true) . time());

        $query_string = "INSERT INTO Tokens(Users_Id, Token) VALUES (:userId_IN, :token)";
        $statementHandler = $this->database_handler->prepare($query_string);

        if ($statementHandler !== false) {

            $statementHandler->bindParam(":userId_IN", $userId_IN);
            $statementHandler->bindParam(":token", $uniqueToken);

            $execSuccess = $statementHandler->execute();

            if ($execSuccess == true) {
                // Token created! Return token
                return $uniqueToken;
            } else {
                $errorMessage = "Execute Failed";
                $errorLocation = "createToken() in Users.php";
            }
        } else {
            $errorMessage = "StatementHandler Failed";
            $errorLocation = "createToken() in Users.php";
        }

        return $this->errorHandler($errorMessage, $errorLocation);
    }

    private function deleteToken($userId_IN)
    {
        /*
        Deletes token

        Return
        - Confirm message on Success
        - Error messages on failed operations

        */

        $query_string = "DELETE FROM Tokens WHERE Users_Id = :userId_IN";
        $statementHandler = $this->database_handler->prepare($query_string);

        if ($statementHandler !== false) {

            $statementHandler->bindParam(":userId_IN", $userId_IN);

            $execSuccess = $statementHandler->execute();

            if ($execSuccess == true) {
                // Token deleted!
                return "deleted";
            } else {
                $errorMessage = "Execute Failed";
                $errorLocation = "deleteToken() in Users.php";
            }
        } else {
            $errorMessage = "StatementHandler Failed";
            $errorLocation = "delete() in Users.php";
        }
        return $this->errorHandler($errorMessage, $errorLocation);
    }

    public function getToken($userId_IN, $username_IN)
    {
        /*
        Get users token whether its already created or not
        
        checktoken() will return:
        - A message, "deleted" if token exist but isn't valid
        - FALSE If token doesn't exist

        getToken() Returns 
        - Token as a string

        */

        if ($this->checkToken($userId_IN) == "deleted") {
            // Token did exist but was deleted. Create a new one
            return $this->createToken($userId_IN, $username_IN);
        }

        if ($this->checkToken($userId_IN) === false) {
            // Token doesn't exist, create a token";
            return $this->createToken($userId_IN, $username_IN);
        }
    }

    private function checkToken($userId_IN)
    {
        /* 
        Checks if token is valid or not.

        If token:
        exist and isn't valid   ->   checkToken returns "deleted"
        doesn't exist           ->   checkToken returns FALSE

        */

        // Check if token is active
        $query_string = "SELECT Date_Updated, Token FROM Tokens WHERE Users_Id = :userId_IN";

        $statementHandler = $this->database_handler->prepare($query_string);
        if ($statementHandler !== false) {

            $statementHandler->bindParam(":userId_IN", $userId_IN);
            $execSuccess = $statementHandler->execute();

            if ($execSuccess === true) {

                $result = $statementHandler->fetch(PDO::FETCH_ASSOC);

                if (!empty($result)) {
                    // Token exist, delete and return a confirmation from deleteToken()
                    return $this->deleteToken($userId_IN);
                } else {
                    // Token doesn't exist
                    return false;
                }
            } else {
                $errorMessage = "Execute Failed";
                $errorLocation = "checkToken() in Users.php";
            }
        } else {
            $errorMessage = "Statementhandler Failed";
            $errorLocation = "checkToken() in Users.php";
        }

        return $this->errorHandler($errorMessage, $errorLocation);
    }

    public function validateToken($token_IN)
    {
        /*
        Checks if token is valid based on our validity time ($this->token_validity_time)

        Returns
        - An updated token through updateToken() if token exists and is active
        - Error message and FALSE if token isnt active or doesnt exist
        - Error messages on failed operations

        */



        // Check if token is active
        $query_string = "SELECT Date_Updated, Token FROM Tokens WHERE Token = :token_IN";

        $statementHandler = $this->database_handler->prepare($query_string);
        if ($statementHandler !== false) {

            $statementHandler->bindParam(":token_IN", $token_IN);
            $execSuccess = $statementHandler->execute();

            if ($execSuccess === true) {

                $result = $statementHandler->fetch(PDO::FETCH_ASSOC);

                if (!empty($result)) {
                    // Token exists
                    $token_timestamp = new DateTime($result['Date_Updated']);
                    $current_timestamp = new DateTime($this->getCurrentTimeFromDB());

                    // Get interval between Date_Updated and Current time:
                    $interval = $token_timestamp->diff($current_timestamp);
                    $interval = $interval->format('%i'); // Format result to minutes

                    if ($interval < $this->token_validity_time) {
                        // Update token, return an updated token through updateToken() on success
                        return $this->updateToken($result['Token']);
                    } else {
                        // Token is not active, return false
                        $errorMessage = "Token is not active";
                        $this->errorHandler($errorMessage);
                        return false;
                    }
                } else {
                    // Token doesn't exist, return false
                    $errorMessage =  "Token doesnt exist";
                    $this->errorHandler($errorMessage);
                    return false;
                }
            } else {
                $errorMessage = "Execute Failed";
                $errorLocation = "checkToken() in Users.php";
            }
        } else {
            $errorMessage = "Statementhandler Failed";
            $errorLocation = "checkToken() in Users.php";
        }

        return $this->errorHandler($errorMessage, $errorLocation);
    }

    private function updateToken($token_IN)
    {
        /*
        Update token time on success

        Returns
        - Token on success
        - Error messages on failed operations

        */


        $query_string = "UPDATE TokensSET Date_Updated = CURRENT_TIMESTAMP WHERE (Token = :token_IN)";
        $statementHandler = $this->database_handler->prepare($query_string);

        if ($statementHandler !== false) {

            $statementHandler->bindParam(":token_IN", $token_IN);
            $execSuccess = $statementHandler->execute();

            if ($execSuccess === true) {
                // Return an updated token
                return $token_IN;
            } else {
                $errorMessage = "Execute Failed";
                $errorLocation = "updateToken() in Users.php";
            }
        } else {
            $errorMessage = "Statementhandler Failed";
            $errorLocation = "updateToken() in Users.php";
        }

        return $this->errorHandler($errorMessage, $errorLocation);
    }

    public function checkTokenRole($token_IN)
    {
        /*
        Check user role from token

        Returns
        - Role name on success
        - Error message if token doesnt exist
        - Error messages on failed operations

        */

        $query_string = "SELECT Roles.Name AS Role FROM Tokens JOIN Users ON Users_Id = Users.Id JOIN Roles ON Roles_Id = Roles.Id WHERE Token = :token_IN";
        $statementHandler = $this->database_handler->prepare($query_string);
        if ($statementHandler !== false) {

            $statementHandler->bindParam(":token_IN", $token_IN);
            $execSuccess = $statementHandler->execute();

            if ($execSuccess === true) {
                $result = $statementHandler->fetch(PDO::FETCH_ASSOC);
                if (!empty($result)) {
                    // Return role as a string
                    return $result['Role'];
                } else {
                    $errorMessage = "Token doesn't exist";
                    return $this->errorHandler($errorMessage);
                }
            } else {
                $errorMessage = "Execute Failed";
                $errorLocation = "checkTokenRole() in Users.php";
            }
        } else {
            $errorMessage = "Statementhandler Failed";
            $errorLocation = "checkTokenRole() in Users.php";
        }
        return $this->errorHandler($errorMessage, $errorLocation);
    }

    public function getUserFromToken($token_IN)
    {

        /*
        Get user id from token

        Returns
        - User id on success
        - Error message if token doesnt exist
        - Error messages on failed operations

        */

        $query_string = "SELECT Users.Id FROM Users JOIN Tokens on Users.Id = Users_Id WHERE Token = :token_IN";
        $statementHandler = $this->database_handler->prepare($query_string);
        if ($statementHandler !== false) {

            $statementHandler->bindParam(":token_IN", $token_IN);
            $execSuccess = $statementHandler->execute();

            if ($execSuccess === true) {
                $result = $statementHandler->fetch(PDO::FETCH_ASSOC);
                if (!empty($result)) {
                    // Return user Id
                    return $result['Id'];
                } else {
                    $errorMessage = "Token doesn't exist";
                    $this->errorHandler($errorMessage);
                    return false;
                }
            } else {
                $errorMessage = "Execute Failed";
                $errorLocation = "getUserFromToken() in Users.php";
            }
        } else {
            $errorMessage = "Statementhandler Failed";
            $errorLocation = "getUserFromToken() in Users.php";
        }
        return $this->errorHandler($errorMessage, $errorLocation);
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
