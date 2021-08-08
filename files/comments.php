<?php

class Comments{

    //Property to hold connection
    private $con;

    //Creating connection object using constuctor
    public function __construct(){
        $this->con = new DBConfig();
    }

    //Function to show comment
    public function showComments(int $pid, int $offset = 0, $limit = 15){
        
        $query = "SELECT * FROM comments WHERE pid = ? ORDER BY cdate LIMIT ?,?;";

        $paramType = "iii";

        $offset = $offset - 1;
        $offset = $offset * $limit;

        $stmt = $this->con->conn->prepare($query);
        $stmt->bind_param($paramType, $pid, $offset, $limit);

        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows >  0){

            //Variable to store all the posts
            $comments = array();

            while ($row = $result->fetch_assoc()){
                $comments[] = $row;
            }

            return $comments;
        }

        return 0;
    }

    //Function to add a new post
    public function addComment(int $uid, int $pid, $date, $content):int{

        $query = 'INSERT INTO comments (uid, pid, cdate, cbody) VALUES (?, ?, ?, ?);';

        $paramType = 'iiss';
        $stmt = $this->con->conn->prepare($query);

        $stmt->bind_param($paramType, $uid, $pid, $date, $content);
        $stmt->execute();

        $lastId = $this->con->conn->insert_id;

        return $lastId;
    }

    //Function to get single comment info
    public function getComment($cid){
        $query = 'SELECT * FROM comments WHERE cid = ? LIMIT 1;';
        $paramType = 'i';

        $stmt = $this->con->conn->prepare($query);

        $stmt->bind_param($paramType, $cid);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows > 0){
            
            while ($row = $result->fetch_assoc()){
                return $row;
            }
        }
        return -1;
    }

    //Function to delete a comment
    public function deleteComment(int $cid){

        $query = 'UPDATE comments SET available = -1 WHERE cid = ?;';
        $paramType = 'i';

        $stmt = $this->con->conn->prepare($query);
        $stmt->bind_param($paramType, $cid);
        $stmt->execute();

    }
}