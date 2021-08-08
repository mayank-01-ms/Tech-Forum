<?php


$pid = isset($_GET['pid']) ? $_GET['pid'] : false;

if ($pid == false){
    header("Location: index.php");
    exit();
}

session_start();
//Checking for session
$loggedIn = isset($_SESSION['loggedIn']) ? $_SESSION['loggedIn'] : false;
if ($loggedIn != true){
    echo 'You are not logged in';
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

if (isset($_POST['edit-post'])){
    if ($loggedIn == true){

        $sessionUlvl = $user->userLevel($_SESSION['uid']);
        $permission = $user->userPermissions($sessionUlvl);

        $found = 0;

        if (!filter_var($pid, FILTER_VALIDATE_INT)){
            echo '404 Not Found <br />
            <a href="index.php">Home page</a>   
            ';
            exit();
        }

        //Fetch post data
        $postData = $post->getPostsInfo($pid);

        if ($postData !== -1){

            //Checking user permission
            $auid = $postData['uid'];

            $available = $postData['available'];
            if ($available == 1){

                if (($permission['post'] == 1) && ($auid == $_SESSION['uid'] || ($sessionUlvl == 4 || $sessionUlvl == 5))){
                                    
                    $bannerOriginalName = $postData['banner'];

                    //Getting form information
                    $title = validData($_POST['p-title']);
                    $category = validData($_POST['p-cat']);
                    $content = trim($_POST['p-body']);
                    $allow_com = isset($_POST['p-com']) ? validData($_POST['p-com']) : 'yes';
                    $pbanner = isset($_FILES['p-banner']) ? $_FILES['p-banner'] : false;
                    $fileOK = 0;

                    //Start validation
                    if (empty($title) || empty($content)){
                        echo 'Some fields are empty';
                        exit();
                    } elseif (strlen($title) < 10){
                        echo 'Title must be of at least 10 charachters';
                        exit();
                    } elseif (strlen($title) > 100){
                        echo 'Title must not be greater than 100 charachters';
                        exit();
                    } elseif (strlen($content) < 50){
                        echo 'Post content must be of at least 50 charachters';
                        exit();
                    }

                    //Checking for valid category
                    $categoryExist = $categoryObj->categoryName($category);
                    if ($categoryExist == -1){
                        echo 'No such category available';
                        exit();
                    }

                    //Else uploading image
                    if ($pbanner !== false){
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
                                    $bannerNewName = uniqid('', true).".".$bActExt;
                                    $destination = 'banners/'.$bannerNewName;
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


                    //Setting up the new url
                    $slug = slugify($title);

                    //Cheking whether banner uploaded or not
                    if ($fileOK == 1){
                        $bannerNameToBeInserted = $bannerNewName;

                        //Remove old banner
                        unlink('banners/'.$bannerOriginalName);
                    } else {
                        $bannerNameToBeInserted = $bannerOriginalName;
                    }

                    //uploading data
                    $post->updatePost($pid, $title, $category, $bannerNameToBeInserted, $content, $allow_com, $slug);
                    header("Location: view-post.php?pid=".$pid);

                } else {
                    echo 'You cannot edit this post.';
                }
            } else {
                echo 'Cannot edit deleted post';
            }
        } else {
            echo 'Post not found';
        }
    } else {
        echo 'You are not logged in';
    }
} else {
    'Something went wrong';
}

