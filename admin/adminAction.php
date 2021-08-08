<?php

//Require databse connection class
require '../files/dbconfig.php';
$con = new DBConfig();

//Require user class to check user is admin
require '../files/user.php';
$user = new User();

//Require classs to get post data
require "../files/posts.php";
$post = new Posts();

//Require admin class
require 'adminclass.php';
$admin = new admin();

//Require notification class
require '../files/notificationClass.php';
$notification = new notification();

//Require functions
require '../includes/functions.php';

//Require meta info for domain
require '../files/meta.php';

//Checking for logged in
session_start();
$loggedIn = isset($_SESSION['loggedIn']) ? $_SESSION['loggedIn'] : false;
if ($loggedIn == false){
    exit();
}

$sessionUserLevel = $user->userLevel($_SESSION['uid']);

if ($sessionUserLevel != 4 && $sessionUserLevel != 5){
    exit();
}

//Feature posts action
if (isset($_POST['action']) && $_POST['action'] == 'feature'){

    $pid = isset($_POST['pid']) ? validData($_POST['pid']) : '';
    $position = isset($_POST['pos']) ? validData($_POST['pos']) : '';
    $newTitle = isset($_POST['title']) ? validData($_POST['title']) : '';
    $description = isset($_POST['des']) ? validData($_POST['des']) : '';

    //Validation
    if (!filter_var($pid, FILTER_VALIDATE_INT)){
        echo 'invalid';
        exit();
    }
    if (!filter_var($position, FILTER_VALIDATE_INT)){
        echo 'invalid';
        exit();
    }
    if (strlen($newTitle) < 10 || strlen($newTitle) > 100){
        echo 'title';
        exit();
    }
    if (strlen($newTitle) < 10 || strlen($newTitle) > 200){
        echo 'des';
        exit();
    }
    if($position < 1 || $position > 3){
        echo 'Incorrect position';
        exit();
    }

    $postData = $post->getPostsInfo($pid);

    if ($postData != -1 && $postData['available'] == 1){
        if ($pid != '' && $position != '' && $newTitle != '' && $description != ''){

            if ($admin->checkFeatured($pid)){
                echo 'exists';
                exit();
            }

            $admin->feature($pid, $newTitle, $description, $position);
            
            //Send a notification
            //Setting message for the user
            $message = 'Your post <a href="'.$metaInfo['domain'].'/view-post.php?pid='.$pid.'">'.$postData['title'].'</a> ' ;
            $message .= 'was featured on home page by <a href="'.$metaInfo['domain'].'/view-profile.php?uid='.$_SESSION['uid'].'">';
            $message .= $user->userName($_SESSION['uid']).'</a>';

            $notification->insertNoti($postData['uid'], $message);

            //Success
            echo 1;
        }

        else {
            echo 0;
        }
    }

    

}

//Change user group 
if (isset($_POST['action']) && $_POST['action'] == 'changeUG'){

    $uid = isset($_POST['uid']) ? validData($_POST['uid']) : '';
    $newUG = isset($_POST['UG']) ? validData($_POST['UG']) : '';

    if (!filter_var($uid, FILTER_VALIDATE_INT) || !filter_var($newUG, FILTER_VALIDATE_INT)){
        echo 'invalid';
        exit();
    }

    $user->userLevel($uid);

    if ($uid != '' && $newUG != ''){

        $userExists = $user->userName($uid);
        $userLevel = $user->userLevel($uid);

        if ($newUG > 6 && $newUG < 0){
            echo 0;
            exit();
        }

        if ($userExists != -1){

            //Checking if he is admin
            if ($sessionUserLevel == 4){
                if ($userLevel == 4 || $userLevel == 5){
                    echo 'admin';
                    exit();
                }else {     
                    if ($_SESSION['uid'] != $uid){                   
                        $admin->changeUG($uid, $newUG);                        
                    }
                    else {
                        echo 'admin';
                        exit();
                    }
                }
            }

            //IF owner
            else
            if ($sessionUserLevel == 5){
                if ($_SESSION['uid'] != $uid){
                    $admin->changeUG($uid, $newUG);
                }else {
                    echo 'admin';
                    exit();
                }
            }

            //Setting message for the user
            $message = 'Your User Group was changed to "'.$user->userGroup($newUG) ;
            $message .= '" by <a href="'.$metaInfo['domain'].'/view-profile.php?uid='.$uid.'">';
            $message .= $user->userName($_SESSION['uid']).'</a>';

            $notification->insertNoti($uid, $message);

            //Success 
            echo 1;
        }
        else {
            echo 2;
        }

    }
}

//Ban user
if (isset($_POST['action']) && $_POST['action'] == 'ban'){

    $uid = isset($_POST['uid']) ? validData($_POST['uid']) : '';

    if (!filter_var($uid, FILTER_VALIDATE_INT)){
        echo 'invalid';
        exit();
    }

    $userExists = $user->userName($uid);
    $userLevel = $user->userLevel($uid);

    if ($uid != ''){

        if ($userExists != -1){

            //Checking if he is admin
            if ($sessionUserLevel == 4){
                if ($userLevel == 4 || $userLevel == 5){
                    echo 'admin';
                    exit();
                }else {    
                    if ($_SESSION['uid'] != $uid){                         
                        $admin->ban($uid);
                    } else {
                        echo 'admin';
                        exit();
                    }                 
                }
            }

            //IF owner
            else
            if ($sessionUserLevel == 5){
                if ($_SESSION['uid'] != $uid){
                    $admin->ban($uid);
                }                
                else {
                    echo 'admin';
                    exit();
                }
            }

            //Setting message for the user
            $message = 'You have been banned from performing actions on the forum ';
            $message .= 'by <a href="'.$metaInfo['domain'].'/view-profile.php?uid='.$uid.'">';
            $message .= $user->userName($_SESSION['uid']).'</a>';

            $notification->insertNoti($uid, $message);

            //Success 
            echo 1;
        }
        else{
            echo 0;
        }

    }
}

//Unfeature
if (isset($_POST['action']) && $_POST['action'] == 'un-feature'){

    $pid = isset($_POST['pid']) ? $_POST['pid'] : '';

    if (!filter_var($pid, FILTER_VALIDATE_INT)){
        echo 'invalid';
        exit();
    }

    if ($pid != ''){

        //Checking if posts exists
        $postexist = $admin->checkFeatured($pid);
        if ($postexist == 0){
            echo 'exists';
            exit();
        }

        else{            
            $admin->unFeature($pid);
            echo 1;
            exit();
        }
    }

}

//Unfeature
if (isset($_POST['action']) && $_POST['action'] == 'recover'){

    $pid = isset($_POST['pid']) ? $_POST['pid'] : '';

    if (!filter_var($pid, FILTER_VALIDATE_INT)){
        echo 'invalid';
        exit();
    }

    if ($pid != ''){

        //Checking if posts exists
        $postexist = $post->getPostsInfo($pid);
        $postexist = $postexist['available'];
        if ($postexist != -1){
            echo 'e';
            exit();
        }

        else{            
            $admin->recover($pid);
            //Setting message for the user

            $postData = $post->getPostsInfo($pid);
            if ($postData != 1){
                $message = 'Your post <a href="'.$metaInfo['domain'].'/view-post.php?pid='.$pid.'">'.$postData['title'].'</a> ' ;
                $message .= 'was recovered by <a href="'.$metaInfo['domain'].'/view-profile.php?uid='.$_SESSION['uid'].'">';
                $message .= $user->userName($_SESSION['uid']).'</a>';

                $notification->insertNoti($postData['uid'], $message);
                echo 1;
                exit();
            }            
        }
    }

}