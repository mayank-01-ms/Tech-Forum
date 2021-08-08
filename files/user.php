<?php

class User{

    //Property to hold connection
    private $con;

    //creating connection object using constructor
    public function __construct(){
        $this->con = new DBConfig();
    }

    //Function to get username and user level
    public function userName(int $uid){

        $query = "SELECT uname FROM users WHERE uid = ? LIMIT 1;";
        $paramType = "i";

        $stmt = $this->con->conn->prepare($query);

        $stmt->bind_param($paramType, $uid);
        $stmt->execute();

        $stmt->store_result();
        $stmt->bind_result($uname);

        if ($stmt->num_rows > 0){

            $stmt->fetch();

            return $uname;

        }
        else {
            return -1;
        }
        
    }

    //Function to get user level
    public function userLevel(int $uid):int{

        $query = "SELECT ulvl FROM users WHERE uid = ? LIMIT 1;";
        $paramType = "i";

        $stmt = $this->con->conn->prepare($query);

        $stmt->bind_param($paramType, $uid);
        $stmt->execute();

        $stmt->store_result();
        $stmt->bind_result($ulvl);

        if ($stmt->num_rows > 0){

            $stmt->fetch();
            return $ulvl;

        }
        else {
            return -1;
        }
    }

    //Function to get user email for editing it
    public function userEmail(int $uid){

        $query = "SELECT uemail FROM users WHERE uid = ? LIMIT 1;";
        $paramType = "i";

        $stmt = $this->con->conn->prepare($query);

        $stmt->bind_param($paramType, $uid);
        $stmt->execute();

        $stmt->store_result();
        $stmt->bind_result($email);

        if ($stmt->num_rows > 0){

            $stmt->fetch();
            return $email;

        }
        else {
            return -1;
        }
    }

    //Function to fetch user group
    public function userGroup(int $ulvl){

        $query = "SELECT ug FROM usergroups WHERE ulvl = ? LIMIT 1;";
        $paramType = "i";

        $stmt = $this->con->conn->prepare($query);

        $stmt->bind_param($paramType, $ulvl);
        $stmt->execute();

        $stmt->store_result();
        $stmt->bind_result($ugrp);

        if ($stmt->num_rows > 0){

            $stmt->fetch();
            return $ugrp;

        }
        else{
            return -1;
        }
    }
    
    //Function to get the profile image details
    public function profileImage(int $uid){

        $query = 'SELECT image FROM userdetails WHERE uid = ? LIMIT 1;';
        $paramType = 'i';

        $stmt = $this->con->conn->prepare($query);

        $stmt->bind_param($paramType, $uid);
        $stmt->execute();

        $stmt->store_result();
        $stmt->bind_result($image);

        if ($stmt->num_rows > 0){

            $stmt->fetch();

            return $image;
            
        } else {
            return 0;
        }
    }

    
    //Function to return the user details
    public function getUserDetails(int $uid){
        //Fetch data from the databse
        $query = "SELECT * FROM userdetails WHERE uid = ? LIMIT 1;";
        $paramType = "i";

        $stmt = $this->con->conn->prepare($query);

        //Binding params
        $stmt->bind_param($paramType, $uid);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows > 0){

            $details = array();

            while ($row = $result->fetch_assoc()){
                $details['image'] = $row['image'];
                $details['firstName'] = $row['firstName'];
                $details['lastName'] = $row['lastName'];
                $details['dor'] = $row['dor'];
                $details['dob'] = $row['dob'];
                $details['city'] = $row['city'];
                $details['lastActive'] = $row['lastActiveTime'];
                $details['interests'] = $row['interests'];
                $details['ig'] = $row['iglink'];
                $details['fb'] = $row['fblink'];
                $details['tw'] = $row['twlink'];
                $details['ln'] = $row['lnlink'];
            }

            return $details;
        }
        
        return -1;
    }


    //Function to check permissions of user
    public function userPermissions(int $ulvl){
        
        $query = 'SELECT * FROM usergroups WHERE ulvl = ? LIMIT 1;';
        $paramType = 'i';

        $stmt = $this->con->conn->prepare($query);
        $stmt->bind_param($paramType, $ulvl);

        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows > 0){

            //variable to store permission details
            $permit = array();

            while ($row = $result->fetch_assoc()){
                $permit['post'] = $row['canPost'];
                $permit['comment'] = $row['canComment'];
                
            }

            return $permit;
        }

        return -1;

    }

    //Function to fetch the posts of a user
    public function userPosts(int $uid, int $offset = 1, int $limit = 15){

        $query = "SELECT * FROM posts WHERE uid = ? ORDER BY pdate desc LIMIT ?,?;";

        $paramType = "iii";

        $offset = $offset - 1;
        $offset = $offset * $limit;

        $stmt = $this->con->conn->prepare($query);
        $stmt->bind_param($paramType, $uid, $offset, $limit);

        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows >  0){

            //Variable to store all the posts
            $posts = array();

            while ($row = $result->fetch_assoc()){
                $posts[] = $row;
            }

            return $posts;
        }

        return 0;

    }

    //Fuctiom to fetch all comments of A user
    public function userComments(int $uid, int $offset = 1, int $limit = 15){

        $query = "SELECT * FROM comments WHERE uid = ? ORDER BY cdate desc LIMIT ?, ?;";

        $paramType = "iii";
        
        $offset = $offset - 1;
        $offset = $offset * $limit;

        $stmt = $this->con->conn->prepare($query);
        $stmt->bind_param($paramType, $uid, $offset, $limit);

        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows >  0){

            //Variable to store all the comments
            $comments = array();

            while ($row = $result->fetch_assoc()){
                $comments[] = $row;
            }

            return $comments;
        }
        else{
            return 0;
        }
        
    }

    //Number of posts of a user
    public function userPostsNo(int $uid):int{

        $query = "SELECT pid FROM posts WHERE uid = ?;";

        $paramType = "i";

        $stmt = $this->con->conn->prepare($query);
        $stmt->bind_param($paramType, $uid);

        $stmt->execute();  

        $result = $stmt->get_result();

        return $result->num_rows;
        
    }

    //Number of comments by a user
    public function userCommentsNo(int $uid):int{

        $query = "SELECT cid FROM comments WHERE uid = ?;";

        $paramType = "i";

        $stmt = $this->con->conn->prepare($query);
        $stmt->bind_param($paramType, $uid);

        $stmt->execute();  

        $result = $stmt->get_result();

        return $result->num_rows;

    }

    //Top posts of a user
    public function userTopPosts(int $uid, int $limit = 5){

        $query = 'SELECT pid, pbanner, ptitle, slug FROM posts WHERE uid = ? ORDER BY views desc LIMIT ?;';

        $paramType = 'ii';

        $stmt = $this->con->conn->prepare($query);
        $stmt->bind_param($paramType, $uid, $limit);

        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows >  0){

            //Variable to store all the posts
            $posts = array();

            while ($row = $result->fetch_assoc()){
                $posts[] = $row;
            }

            return $posts;
        }

        return 0;
    }

    //Function to update user
    public function updateUser($uid, $uname, $email){

        $query = 'UPDATE users SET uname = ?, uemail = ? WHERE uid = ? LIMIT 1;';
        $paramType = 'ssi';
        
        $stmt = $this->con->conn->prepare($query);
        $stmt->bind_param($paramType, $uname, $email, $uid);

        $stmt->execute();
    }

    //Function to update user details
    public function updateUserDetails($uid, $fname, $lname, $city, $dob, $ig, $fb, $tw, $ln, $interests){

        $query = 'UPDATE userdetails SET firstName = ?, lastName = ?, city = ?, dob = ?, 
        iglink = ?, fblink = ?, twlink = ?, lnlink = ?, interests = ? WHERE uid = ? LIMIT 1;';
        $paramType = 'sssssssssi';
        
        $stmt = $this->con->conn->prepare($query);
        $stmt->bind_param($paramType, $fname, $lname, $city, $dob, $ig, $fb, $tw, $ln, $interests, $uid);

        $stmt->execute();
    }

    //Function to remove profile picture
    public function removePic(int $uid){

        $query = 'SELECT image FROM userdetails WHERE uid = '.$uid.' LIMIT 1;';
        $result = $this->con->conn->query($query);

        if ($result->num_rows > 0){
            $image = $result->fetch_assoc();

            if ($image !== 0){
                $query2 = 'UPDATE userdetails SET image = "profile.png" WHERE uid = '.$uid.';';
                $this->con->conn->query($query2);

                unlink('profileImages/'.$image['image']);
                return 1;
            }
        }

        return -1;
    }

    //Function to set profile picture
    public function setProfile(int $uid, $name){

        $query = 'SELECT image FROM userdetails WHERE uid = '.$uid.' LIMIT 1;';
        $result = $this->con->conn->query($query);

        if ($result->num_rows > 0){

            $query2 = 'UPDATE userdetails SET image = ? WHERE uid = ?;';
            $stmt = $this->con->conn->prepare($query2);
            $stmt->bind_param('si', $name, $uid);

            $stmt->execute();

            return 1;
            
        }

        return -1;
    }

    //function to register user
    // public function insertUser($uid, $fname, $lname, $dor){

    //     $image = 'profile.png';
    //     $query = 'INSERT INTO userdetails (uid, image, firstName, lastName, dor, lastActiveTime) VALUES (?, ?, ?, ?, ?, ?);';

    //     $stmt = $this->con->conn->prepare($query);

    //     $stmt->bind_param('isssss', $uid, $image, $fname, $lname, $dor, $dor);
    //     $stmt->execute();
    // }

}
