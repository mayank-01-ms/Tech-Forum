<?php

class Categories{

    //Property to hold connection
    private $con;

    //Creating connection object using constructor
    public function __construct(){
        $this->con = new DBConfig();
    }

    //Function to display all categories
    public function getCategories(){

        $query = "SELECT * FROM categories ORDER BY cid LIMIT 15;";

        $result = $this->con->conn->query($query);

        //Varaible to store data
        $category = array();

        if ($result->num_rows > 0){
            while ($row = $result->fetch_assoc()){
                $category[] = $row;
            }

            return $category;
        }

        else {
            return -1;
        }
    }

    //Function to the get the category name
    public function categoryName(int $cat){
        $query = "SELECT cname FROM categories WHERE cid = ? LIMIT 1;";
        $paramType = "i";

        $stmt = $this->con->conn->prepare($query);

        $stmt->bind_param($paramType, $cat);
        $stmt->execute();

        $stmt->store_result();
        $stmt->bind_result($category);

        if ($stmt->num_rows > 0){

            $stmt->fetch();
            return $category;

        }

        return -1;
        
    }

    //function to get posts
    public function getPosts(int $cat_id, int $offset = 1, int $limit = 15){

        $query = 'SELECT * FROM posts WHERE pcat = ? LIMIT ?, ?';
        $paramType = "iii";
        
        $offset = $offset - 1;
        $offset = $offset * $limit;

        $stmt = $this->con->conn->prepare($query);
        $stmt->bind_param($paramType, $cat_id, $offset, $limit);

        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows >  0){

            //Variable to store all the comments
            $posts = array();

            while ($row = $result->fetch_assoc()){
                $posts[] = $row;
            }

            return $posts;
        }
        else{
            return 0;
        }
    }

    //Total posts in a category
    public function totalPosts(int $cat_id):int{
        $query = 'SELECT pid FROM posts WHERE pcat = ?;';

        $stmt = $this->con->conn->prepare($query);
        $stmt->bind_param('i', $cat_id);

        $stmt->execute();

        $result = $stmt->get_result();

        return $result->num_rows;
    }

     //Top posts of a categoey
     public function topPosts(int $category_id, int $limit = 5){

        $query = 'SELECT pid, pbanner, ptitle, slug FROM posts WHERE pcat = ? ORDER BY views desc LIMIT ?;';

        $paramType = 'ii';

        $stmt = $this->con->conn->prepare($query);
        $stmt->bind_param($paramType, $category_id, $limit);

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
    //Function to add a category

    //Function to edit a category

    //Function to delete a category
}