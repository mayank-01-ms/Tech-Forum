<?php

//Check isset for the button
session_start();
//Checking for session
$loggedIn = isset($_SESSION['loggedIn']) ? $_SESSION['loggedIn'] : false;

if ($loggedIn == false){
    header("Location: index.php");
    exit();
}

//Require database connection
require "files/dbconfig.php";
$con = new DBConfig();

//Require class user to get user details
require "files/user.php";
$user = new User();

//Require classs to get post data
require "files/posts.php";
$post = new Posts();

//Including categories files
include "files/categories.php";
$categoryObj = new Categories();

//Require the functions page
require "includes/functions.php";

if ($loggedIn == true){
    $userlvl = $user->userLevel($_SESSION['uid']);
    $permission = $user->userPermissions($userlvl);
    $canPost = $permission['post'];

    if ($canPost == 1){
        
        //Initialising varables
        $uid = $_SESSION['uid'];
        $title = validData($_POST['p-title']);
        $category = validData($_POST['p-cat']);
        $body = trim($_POST['p-body']);

        $allowCom = isset($_POST['p-com']) ? validData($_POST['p-com']) : 'yes';
        $banner = isset($_FILES['p-banner']) ? $_FILES['p-banner'] : false;

        //variable to check whether banner uploaded or not
        $fileOK = 0;

        //Validating the data
        if (strlen($title)<10){
            echo "Title must have at least 10 charachters";
            exit();
        }elseif (strlen($body)<50){
            echo "Body must contain at least 50 charachters";
            exit();
        } 

         //Checking for valid category
         $categoryExist = $categoryObj->categoryName($category);
         if ($categoryExist == -1){
             echo 'No such category available';
             exit();
         }
        
        //Uplooading banner
        if ($banner != false){
            $bannerName = $_FILES['p-banner']['name'];
            $tmpLocation = $_FILES['p-banner']['tmp_name'];
            $bannerSize = $_FILES['p-banner']['size'];
            $bannerError = $_FILES['p-banner']['error'];

            $bExt = explode('.', $bannerName);
            $bActExt = strtolower(end($bExt));

            $permitted = array('jpg', 'jpeg', 'png', 'gif');
            
            if (in_array($bActExt, $permitted)){
                if  ($bannerError === 0){
                    if ($bannerSize < 1000000){
                        $bNewName = uniqid('', true).".".$bActExt;
                        $destination = 'banners/'.$bNewName;
                        move_uploaded_file($tmpLocation, $destination);

                        //The banner was uploaded sucessfully
                        $fileOK = 1;
                    }
                    else {
                        echo "Max image size allowed is 1 MB";
                    }
                }
                else {
                    echo "There was an error uploading the image";
                }
            }
            else{
                echo "File type not allowed";
            }
        }

        //Uploading data in database
        if ($fileOK == 1){

            //setting date
            date_default_timezone_set("Asia/Calcutta");
            $date = date("Y-m-d H:i:s");

            //Setting up the SEO URL
            $slug = slugify($title);
            $id = $post->addPost($uid, $category, $title, $date, $bNewName, $body, $allowCom, $slug);
            header("Location: view-post.php?pid=".$id);
        } else {
            echo 'There was an error uploading the banner.';
        }

    }
} else {
    echo 'You are not logged in';
}


