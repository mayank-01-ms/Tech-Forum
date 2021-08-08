<?php

class admin{

    //Property to hold connection
    private $con;

    //creating connection object using constructor
    public function __construct(){
        $this->con = new DBConfig();
    }

    //Method for featuring posts
    public function feature(int $pid, $title, $description, $position){

        date_default_timezone_set("Asia/Calcutta");
        $dof = date("Y-m-d H:i:s");

        $query = 'INSERT INTO featured (pid, newTitle, description, position, dof) VALUES (?, ?, ?, ?, ?);';
        
        $stmt = $this->con->conn->prepare($query);
        $stmt->bind_param('issis', $pid, $title, $description, $position, $dof);
        $stmt->execute();
    }

    //Method to unfeature a post
    public function unFeature(int $pid){

        $query = 'DELETE FROM featured WHERE pid = ?;';
        
        $stmt = $this->con->conn->prepare($query);
        $stmt->bind_param('i', $pid);
        $stmt->execute();

    }

    //Check whether a posts is featured
    public function checkFeatured(int $pid){

        $query = "SELECT pid FROM featured WHERE pid = ?;";
        $paramType = "i";

        $stmt = $this->con->conn->prepare($query);

        $stmt->bind_param($paramType, $pid);
        $stmt->execute();

        $stmt->store_result();

        return $stmt->num_rows;
        
    }

    //Method to ban a user
    public function ban(int $uid){

        $query = 'UPDATE users SET ulvl = -1 WHERE uid = ?;';
        
        $stmt = $this->con->conn->prepare($query);
        $stmt->bind_param('i', $uid);
        $stmt->execute();

    }

    //Method to change user group
    public function changeUG(int $uid, int $newlvl){

        $query = 'UPDATE users SET ulvl = ? WHERE uid = ?;';
        
        $stmt = $this->con->conn->prepare($query);
        $stmt->bind_param('ii', $newlvl, $uid);
        $stmt->execute();

    }

    //Method to view admins
    public function viewAdmins(){

        $query = 'SELECT * FROM users WHERE ulvl = 4;';

        $stmt = $this->con->conn->query($query);

        if ($stmt->num_rows >  0){

            //Variable to store all the posts
            $admins = array();

            while ($row = $stmt->fetch_assoc()){
                $admins[] = $row;
            }

            return $admins;
        }

        return 0;
    }

    //Method to recover posts
    public function recover($pid){

        $query = 'UPDATE posts SET available = 1 WHERE pid = ?;';

        $stmt = $this->con->conn->prepare($query);
        $stmt->bind_param('i', $pid);
        $stmt->execute();
    }

}