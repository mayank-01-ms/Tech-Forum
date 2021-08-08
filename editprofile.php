<?php

session_start();

//Require database connection
require "files/dbconfig.php";
$con = new DBConfig();

//Require class user to get user details
require "files/user.php";
$user = new User();

//Require authenticationn file
require "files/authentication.php";
$auth = new authentication();

//Require the functions page
require "includes/functions.php";

$loggedIn = isset($_SESSION['loggedIn']) ? $_SESSION['loggedIn'] : false;

if (isset($_POST['update'])){

    $uid = $_SESSION['uid'];

    if ($loggedIn == true){
        if ($uid){
            
            //Fetch the information
            $fname = ucwords(validData($_POST['fname']));
            $lname = ucwords(validData($_POST['lname']));
            $dob = validData($_POST['dob']);
            $city = ucwords(validData($_POST['city']));
            $interests = validData($_POST['interests']);
            $ig = trim($_POST['ig']);
            $fb = trim($_POST['fb']);
            $tw = trim($_POST['tw']);
            $ln = trim($_POST['ln']);

            //Validating data

            $regex = '/^[a-zA-Z]*$/';
            if (strlen($fname) < 3 && strlen($fname) < 30){
                echo 'First name must be between 3 - 30 charachters';
                exit();
            }
            if (strlen($lname) < 3 && strlen($lname) < 30){
                echo 'Last name must be between 3 - 30 charachters';
                exit();
            }

            if(!preg_match($regex, $fname) || !preg_match($regex, $lname)){
                echo 'Invalid charachters in name';
                exit();
            }

            if (!empty($city)){
                if (!preg_match($regex, $city)){
                    echo 'Invalid charachters in city';
                    exit();
                }
            }

            if (!empty($ig)){
                if (!filter_var($ig, FILTER_VALIDATE_URL)){
                    echo 'Invalid URL';
                    exit();
                }
            }

            if (!empty($fb)){
                if (!filter_var($fb, FILTER_VALIDATE_URL)){
                    echo 'Invalid URL';
                    exit();
                }
            }

            if (!empty($tw)){
                if (!filter_var($tw, FILTER_VALIDATE_URL)){
                    echo 'Invalid URL';
                    exit();
                }
            }

            if (!empty($ln)){
                if (!filter_var($ln, FILTER_VALIDATE_URL)){
                    echo 'Invalid URL';
                    exit();
                }
            }

            // if (!empty($dob)){

            //     $dobCheck = date_parse($dob);
            //     if (!checkdate($dobCheck['month'], $dobCheck['day'], $dobCheck['year'])){
            //         echo 'Invalid Date';
            //         exit();
            //     }

            // }

            else {

                if ($dob == ''){
                    $dob = NULL;
                }
                if ($ig == ''){
                    $ig = 0;
                }
                if ($fb == ''){
                    $fb = 0;
                }
                if ($tw == ''){
                    $tw = 0;
                }
                if ($ln == ''){
                    $ln = 0;
                }
                $user->updateUserDetails($uid, $fname, $lname, $city, $dob, $ig, $fb, $tw, $ln, $interests);
                header('Location: view-profile.php?uid='.$uid);
            }


        } else {
            echo 'You cannot edit other person profile';
        }
    } else{
        echo 'You are not logged in';
    }
} else {
    header('Location: index.php');
}
