<?php

class authentication{

    //Property to hold connection
    private $con;

    //creating connection object using constructor
    public function __construct(){
        $this->con = new DBConfig();
    }

    //Function to check username availability
    public function checkUsername($name){
        
        $query = 'SELECT uname FROM users WHERE uname = ? LIMIT 1;';
        $paramType = 's';

        $stmt = $this->con->conn->prepare($query);

        $stmt->bind_param($paramType, $name);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows > 0){
            return -1;
        }

        return 1;
    }

    //Function to check email availability
    public function checkMail($name){
        
        $query = 'SELECT uemail FROM users WHERE uemail = ? LIMIT 1;';
        $paramType = 's';

        $stmt = $this->con->conn->prepare($query);

        $stmt->bind_param($paramType, $name);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows > 0){
            return -1;
        }

        return 1;
    }

    //Function to change password
    public function changePassword(int $uid, $oldPwd, $newPwd){

        $query = 'SELECT uid, upwd FROM users WHERE uid = ? LIMIT 1;';
        $paramType = "i";

        $stmt = $this->con->conn->prepare($query);

        $stmt->bind_param($paramType, $uid);
        $stmt->execute();

        $result = $stmt->get_result();

        $row = $result->fetch_assoc();

        if ($result->num_rows > 0){

            $pwdCheck = password_verify($oldPwd, $row['upwd']);
            if ($pwdCheck == 0){
                return 0;
            } elseif ($pwdCheck == 1){
                $query = 'UPDATE users SET upwd = ? WHERE uid = ? LIMIT 1;';
                $paramType = "si";

                $hashedPassword = password_hash($newPwd, PASSWORD_DEFAULT);
                $stmt = $this->con->conn->prepare($query);

                $stmt->bind_param($paramType, $hashedPassword, $uid);
                $stmt->execute();

                return 1;
            }
        } else {
            return -1;
        }
    }

    public function lastActive(int $uid){
        date_default_timezone_set("Asia/Calcutta");
        $date = date("Y-m-d H:i:s");

        $query = 'UPDATE userdetails SET lastActiveTime = ? WHERE uid = ?;';
        $paramType = 'si';
        
        $stmt = $this->con->conn->prepare($query);
        $stmt->bind_param($paramType, $date, $uid);

        $stmt->execute();
    }

    //Get email using

    //Get uid using mail and uid using mail or name
    public function getUIDmail($email, $uname){
        $query = 'SELECT uid, uemail FROM users WHERE uemail = ? OR uname = ? LIMIT 1;';
        $paramType = 'ss';

        $stmt = $this->con->conn->prepare($query);

        $stmt->bind_param($paramType, $email, $uname);
        $stmt->execute();

        $result = $stmt->get_result();

        $row = $result->fetch_assoc();

        if ($result->num_rows > 0){

            return $row;
        }

        return -1;
    }

    //Delete tokens for password reset
    public function deleteToken($uid){
        $query = 'DELETE FROM pwdreset WHERE uid = ?;';
        $paramType = 'i';
        
        $stmt = $this->con->conn->prepare($query);
        $stmt->bind_param($paramType, $uid);

        $stmt->execute();
    }
    
    //Method to insert new token into the database
    public function insertToken($uid, $email, $selector, $token, $expires){
        $query = 'INSERT INTO pwdreset (uid, email, selector, token, expires) VALUES (?, ?, ?, ?, ?);';
        $paramType = 'issss';
        
        $stmt = $this->con->conn->prepare($query);
        $stmt->bind_param($paramType, $uid, $email, $selector, $token, $expires);

        $stmt->execute();
    }

    //Method to fetch the password reset data
    public function pwdResetData($selector){
        $query = 'SELECT * FROM pwdreset WHERE selector = ? LIMIT 1;';
        $paramType = 's';

        $stmt = $this->con->conn->prepare($query);

        $stmt->bind_param($paramType, $selector);
        $stmt->execute();

        $result = $stmt->get_result();

        $row = $result->fetch_assoc();

        if ($result->num_rows > 0){

            return $row;
        }

        return -1;
    }

    //method to update password if forgotten
    public function updatePassword(int $uid, $password){
        $query = 'UPDATE users SET upwd = ? WHERE uid = ? LIMIT 1;';
        $paramType = "si";

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->con->conn->prepare($query);

        $stmt->bind_param($paramType, $hashedPassword, $uid);
        $stmt->execute();

        return 1;
    }
}
