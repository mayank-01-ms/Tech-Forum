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

//Require notification class
require 'files/notificationClass.php';
$notification = new notification();

if ($loggedIn == true){

    $sessionUlvl = $user->userLevel($_SESSION['uid']);

    $title = "Post not found";
    $found = 0;

    if (!filter_var($pid, FILTER_VALIDATE_INT)){
        echo 'Error occured <br />
        <a href="index.php">Home page</a>   
        ';
        exit();
    }

    //Fetch post data
    $postData = $post->getPostsInfo($pid);
    $posttitle = $postData['title'];
    $uidfornotidelete = $postData['uid'];
    $userlevelofauthor = $user->userLevel($uidfornotidelete);

    if ($postData != -1){

        //Checking user permission
        $auid = $postData['uid'];

        $available = $postData['available'];
        if ($available == 1){

            if (($auid == $_SESSION['uid']) || ($sessionUlvl == 4 || $sessionUlvl == 5)){
                
                $deletemessage = 'Your post '.$posttitle.' was deleted by <a href="view-profile.php?uid='.$_SESSION['uid'].'">';
                $deletemessage .= $user->userName($_SESSION['uid']).'</a>';

                if ($userlevelofauthor == 5){
                    echo 'You are not allowed to delete post.';
                    exit();
                }

                if ($userlevelofauthor != 5){ 

                    //Set available to -1
                    $post->deletePost($pid);
                    $notification->insertNoti($uidfornotidelete, $deletemessage);                        
                    
                }
                if ($userlevelofauthor == 5){
                    if ($user->userLevel($_SESSION['uid']) == 5){
                        $post->deletePost($cid);
                        $notification->insertNoti($uidfornotidelete, $deletemessage);
                    }
                } 
                header("Location: index.php");
                
            } else {
                echo 'You cannot delete this post.';
            }
        }
    } else {
        echo 'Post not found';
    }
} else {
    echo 'You are not logged in';
}