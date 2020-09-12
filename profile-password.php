<?php

//Require database connection
require "files/dbconfig.php";
$con = new DBConfig();

//Require authentication class
require 'files/authentication.php';
$auth = new authentication;

//Require class user to get user details
require "files/user.php";
$user = new User();

session_start();
$loggedIn = isset($_SESSION['loggedIn']) ? $_SESSION['loggedIn'] : false;
if ($loggedIn == false){
    header("Location: index.php");
}

if ($loggedIn == true){
    if (isset($_POST['action']) && $_POST['action'] == 'change-password'){

        $oldPassword = $_POST['oPassword'];
        $newPassword = $_POST['newPassword'];
        $cPassword = $_POST['cPassword'];

        $uid = $_SESSION['uid'];

        if ($newPassword !== $cPassword){
            echo 'mismatch';
            exit();
        }

        $updatePassword = $auth->changePassword($uid, $oldPassword, $newPassword);
        if ($updatePassword == 0) {
            echo 0;
            exit();
        }

        if ($updatePassword == -1){
            echo -1;
            exit();
        }

        if ($updatePassword == 1){
            echo 1;

            //Setting notification for the user
            $message = 'Your password was successfully changed.';

            $notification->insertNoti($uid, $message);

            exit();
        }

    }

    if (isset($_POST['action']) && $_POST['action'] == 'removeProfile'){

        $uid = $_SESSION['uid'];
        $res = $user->removePic($uid);
        
        if ($res == 1){
            echo 1;
        } elseif ($res == -1){
            echo -1;
        }
    }
} 
