<?php

session_start();

$loggedIn = isset($_SESSION['loggedIn']) ? $_SESSION['loggedIn'] : false;

//Require database connection
require "files/dbconfig.php";
$con = new DBConfig();

//Require class user to get user details
require "files/user.php";
$user = new User();

//Require the functions page
require "includes/functions.php";

//Require authentication class to check valid user
require 'files/authentication.php';
$auth = new authentication;

//Require meta file
require 'files/meta.php';

if (isset($_POST['action']) && $_POST['action'] == 'rp'){

    $name = validData($_POST['name']);

    $usernameExists = $auth->checkUsername($name);

    if ($usernameExists != -1){
        $mailExists = $auth->checkMail($name);
        if ($mailExists != -1){
            echo 0;
            exit();
        }
    }

    $selector = bin2hex(random_bytes(8));
    $token = random_bytes(32);

    $link = $metaInfo['domain'].'/reset-password.php?selector='.$selector.'&validator='.bin2hex($token);

    date_default_timezone_set("Asia/Calcutta");
    $expires = date("U") + 900;

    $details = $auth->getUIDmail($name, $name);
    $email = $details['uemail'];
    $uid = $details['uid'];
    $userDetails = $user->getUserDetails($uid);

    //Delete existing tokens
    $auth->deleteToken($uid);

    $hashedToken = password_hash($token, PASSWORD_DEFAULT);

    //Inserting the tokens
    $auth->insertToken($uid, $email, $selector, $hashedToken, $expires);

    //Sending the mail
    $to = $email;
    $subject = 'Password reset request for your account on '.$metaInfo['keywords'];
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
            A request for resetting your account password was generated. Click the button below to reset the password.
             Please make sure that you don\'t share the email or link to anyone.
        </p>';
    $message .= '<div id="button" style=" color: #fff; margin: 15px auto; width: 210px;">
        <br>
        <a href="'.$link.'" style="display: block; color: #fff; text-decoration: none; background: #00c6ff; max-height: 50px; max-width: 200px; padding: 15px 50px; border-radius: 5px;">                
            Reset Password
        </a>
        <br>
        </div>
        <p>
            If you didn\'t requested for password reset, kindly ignore this email. Please make sure that this email is only valid for 15 minutes.
        </p>
        <div style="border-top: 1px solid #333;"></div>';
    $message .= '<p>Copy the below link on to your browser, if you are having any issue clicking the password reset button</p>
        <div id="link">
            <a href="'.$link.'">'.$link.'</a>
        </div>

        </div>
        </div>';    
    $message .= '</body>
    </html>';

    $headers = "From: "."mswebsite01@gmail.com"."\r\n";
    $headers .= "Content-type: text/html"."\r\n";

    mail($to, $subject, $message, $headers);

    echo 1;
  
}
