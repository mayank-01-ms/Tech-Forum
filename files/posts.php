<?php

//Require DB connection file
// require "dbconfig.php";

class Posts {
    
    //Property to hold connection
    private $con;

    //Creating connection object using constructor
    public function __construct(){
        $this->con = new DBConfig();
    }
    
    //function to get info about a post
    public function getPostsInfo($pid){

        $query = "SELECT * FROM posts WHERE pid = ?";
        $paramType = "i";

        $stmt = $this->con->conn->prepare($query);

        $stmt->bind_param($paramType, $pid);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows > 0){

            //Variable to store posts data
            $postData;
            while ($row = $result->fetch_assoc()){
                $postData['pid'] = $row['pid'];
                $postData['uid'] = $row['uid'];
                $postData['cat'] = $row['pcat'];
                $postData['title'] = $row['ptitle'];
                $postData['date'] = $row['pdate'];
                $postData['banner'] = $row['pbanner'];
                $postData['content'] = $row['pcontent'];
                $postData['views'] = $row['views'];
                $postData['available'] = $row['available'];
                $postData['allow-com'] = $row['allow_com'];
                $postData['slug'] = $row['slug'];
            }

            return $postData;
        }

        else {
            return -1;
        }
    }

    //Function to add a new post
    public function addPost($uid, $cat, $title, $date, $banner, $content, $allowCom, $slug):int{

        $query = 'INSERT INTO posts (uid, pcat, ptitle, pdate, pbanner, pcontent, allow_com, slug) VALUES (?, ?, ?, ?, ?, ?, ?, ?);';

        $paramType = 'iissssss';
        $stmt = $this->con->conn->prepare($query);

        $stmt->bind_param($paramType, $uid, $cat, $title, $date, $banner, $content, $allowCom, $slug);
        $stmt->execute();

        $lastId = $this->con->conn->insert_id;

        return $lastId;
    }

    //Function to update a post
    public function updatePost($pid, $title, $cat, $banner, $content, $allowCom, $slug){

        $query = 'UPDATE posts SET pcat = ?, ptitle = ?, pbanner = ?, pcontent = ?, allow_com = ?, slug = ? WHERE pid = ?;';
        $paramType = 'isssssi';
        $stmt = $this->con->conn->prepare($query);

        $stmt->bind_param($paramType, $cat, $title, $banner, $content, $allowCom, $slug,$pid);
        $stmt->execute();

    }

    //Function to count number of comments in a post
    public function noComments($pid):int{

        $query = "SELECT cid FROM comments WHERE pid = ?;";

        $paramType = "i";

        $stmt = $this->con->conn->prepare($query);
        $stmt->bind_param($paramType, $pid);

        $stmt->execute();  

        $result = $stmt->get_result();

        return $result->num_rows;
    }

    //Function to update a view by one
    public function updateView(int $pid){

        $checkQuery = 'SELECT available FROM posts WHERE pid = ?;';
        $checkStmt = $this->con->conn->prepare($checkQuery);

        $checkStmt->bind_param('i', $pid);
        $checkStmt->execute();

        $checkStmt->store_result();
        $checkStmt->bind_result($available);

        if ($checkStmt->num_rows > 0){

            $checkStmt->fetch();

            if ($available === 1){
                $query = 'UPDATE posts SET views = views + 1 WHERE pid = ?;';
                $paramType = 'i';

                $stmt = $this->con->conn->prepare($query);

                $stmt->bind_param($paramType, $pid);
                $stmt->execute();
            }
        }
    }

    //Function to delete a post
    public function deletePost(int $pid){

        $query = 'UPDATE posts SET available = -1 WHERE pid = ?;';
        $paramType = 'i';

        $stmt = $this->con->conn->prepare($query);
        $stmt->bind_param($paramType, $pid);
        $stmt->execute();

    }
}