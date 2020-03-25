<?php
include("../../config/database_handler.php");


class User
{
    private $database_handler;
    private $username;
    private $token_validity_time = 15; // Minutes

    public function __construct($datebase_handler_IN)
    {
        $this->database_handler = $datebase_handler_IN;
    }


    public function addUser($username_IN, $password_IN, $email_IN, $roleId_IN)
    {
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
        $query_string = "SELECT Username FROM Users WHERE Username = :username_IN";

        $statementHandler = $this->database_handler->prepare($query_string);

        if ($statementHandler !== false) {

            $statementHandler->bindParam(":username_IN", $username_IN);
            $execSuccess = $statementHandler->execute();

            if ($execSuccess === true) {
                $result = $statementHandler->fetch(PDO::FETCH_ASSOC);

                print_r($result);

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
            }
        }
    }

    public function getUser($column, $value, $column2 = false, $value2 = false)
    {

        /*

        Column = which column to match with value
        Value  = which value match with column

        Example:
        getUser(Username, "Janne Ball") will return a User with username "Janne Ball". 

         */

        // Init twoColumns
        $twoColumns = false;

        $query_string = "SELECT Users.Id, Username, Email, Date_Created, Name AS Role FROM Users JOIN Roles ON Roles_Id = Roles.Id ";

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
                $errorMessage = "Second column is not valid";
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
                    $errorMessage = "No match in DB";
                    $errorLocation = "getUser() in Users.php";
                }
            } else {
                $errorMessage = "Execute failed";
                $errorLocation = "getUser() in Users.php";
            }
        } else {
            $errorMessage = "Statementhandler failed";
            $errorLocation = "getUser() in Users.php";
        }

        return $this->errorHandler($errorMessage, $errorLocation);
    }

    public function loginUser($username_IN, $password_IN)
    {

        // $query_string = "SELECT"

    }

    private function errorHandler($message_IN, $errorLocation_IN)
    {
        $returnObject = new stdClass;

        $returnObject->message = $message_IN;
        $returnObject->location = $errorLocation_IN;
        echo json_encode($returnObject);
    }
}
