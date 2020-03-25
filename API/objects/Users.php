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

                $last_inserted_id = $this->database_handler->lastInsertId();
                
                $return = $this->getUser($last_inserted_id);
                $message = "{$return['Role']} '{$return['Username']}' inserted to DB successfully";
                return $message;
            }
        }
    }

    private function getUser($userId_IN)
    {
        $query_string = "SELECT Users.Id, Username, Email, Date_Created, Name AS Role FROM Users JOIN Roles ON Roles_Id = Roles.Id WHERE Users.Id = :userId_IN";

        $statementHandler = $this->database_handler->prepare($query_string);

        if ($statementHandler !== false) {
            $statementHandler->bindParam(":userId_IN", $userId_IN);
            $execSuccess = $statementHandler->execute();

            if ($execSuccess === true) {

                $result = $statementHandler->fetch(PDO::FETCH_ASSOC);

                // Kolla ifall resultatet inte är tomt
                if(!empty($result)){
                    return $result;
                }
            }

        }
    }

    private function errorHandler($message_IN, $errorLocation_IN)
    {
        $returnObject = new stdClass;

        $returnObject->message = $message_IN;
        $returnObject->location = $errorLocation_IN;
        echo json_encode($returnObject);
    }
}
