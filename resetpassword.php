<?php

session_start();

$loggedIn = isset($_SESSION['loggedIn']) ? $_SESSION['loggedIn'] : false;

//Require database connection
require "files/dbconfig.php";
$con = new DBConfig();

//Require class user to get user details
require "files/user.php";
$user = new User();

//Require authentication class to check valid user
require 'files/authentication.php';
$auth = new authentication;

//Require meta file
require 'files/meta.php';

//Require notification class
require 'files/notificationClass.php';
$notification = new notification();

if (isset($_POST['action']) && $_POST['action'] == 'rp'){

    $password = $_POST['password'];
    $cpassword = $_POST['cpassword'];
    $selector = isset($_POST['selector']) ? $_POST['selector'] : false;
    $validator = isset($_POST['validator']) ? $_POST['validator'] : false;

    if (empty($password) || empty($cpassword)){
        echo -1;
        exit();
    } elseif ($password != $cpassword) {
        echo 'mismatch';
        exit();
    }

    if ($selector == false || $validator == false){
        echo 0;
        exit();
    } else {
        if (ctype_xdigit($selector) !== false && ctype_xdigit($validator) !== false){
                        
            date_default_timezone_set("Asia/Calcutta");
            $currentDate = date("U");

            $pwdResetData = $auth->pwdResetData($selector);

            if ($pwdResetData !== -1){
                $tokenBinary = hex2bin($validator);
                $tokenCheck = password_verify($tokenBinary, $pwdResetData['token']);

                if ($tokenCheck === false){

                    echo 2;
                    exit();

                } elseif ($tokenCheck === true){

                    if ($currentDate > $pwdResetData['expires']){

                        echo 2;
                        exit();

                    } elseif ($currentDate < $pwdResetData['expires']) {

                        $auth->updatePassword($pwdResetData['uid'], $password);

                        //Setting message for the user
                        $message = 'Your password was reset successfully. ' ;                        
                        $notification->insertNoti($pwdResetData['uid'], $message);

                        //Setting up the mail to notify the user
                        $to = $pwdResetData['email'];
                        $userDetails = $user->getUserDetails($pwdResetData['uid']);
                        $subject = 'Password changed successfully of your account on '.$metaInfo['keywords'];
                        $userFirstName = $userDetails['firstName'];

                        $message = '<html>
                            <body>';
                        $message .= '<div id="wrapper" style="background: #f2f2f2; padding: 20px 5px;">
                            <div id="main" style="background: #fff; padding: 5px;">

                            <div style="border-top: 2px solid #00c6ff; margin-bottom: 15px;"></div>
                            <div id="logo" style="float: left; width: 50px; height: 50px; margin-right: 15px;">
                                <img src="'.$metaInfo['domain'].'/images/logo.png" alt="logo" height="50px" width="50px">
                            </div>
                            <div id="logo_text" style="margin-top: 30px; margin-left: 65px;">
                                <span style="font-weight: 600; color: #00c6ff;">Tech</span>
                                <span style="font-weight: 600; color: #00c6ff;">@</span>
                                <span style="font-weight: 600; color: #00c6ff;">GLANCE</span>
                            </div>';
                        $message .= '<div style="font-weight: bold; clear: both; margin-top: 45px;">
                            Hi '.$userFirstName.',
                            </div>
                            <p>
                                Your account password was reset successfully. In case you did not perform this action, please reply to this mail with your username.

                            </p>';
                        $message .= '
                            </div>
                            </div>';    
                        $message .= '</body>
                        </html>';

                        $headers = "From: "."mswebsite01@gmail.com"."\r\n";
                        $headers .= "Content-type: text/html"."\r\n";

                        mail($to, $subject, $message, $headers);

                        echo 1;
                    }
                    //Delete tokens in both the case since they are of no use once they are used or expired
                    $auth->deleteToken($pwdResetData['uid']);
                }
            }
        }
    }
}
?>