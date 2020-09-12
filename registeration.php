<?php

session_start();

$loggedIn = isset($_SESSION['loggedIn']) ? $_SESSION['loggedIn'] : false;

if ($loggedIn == true){
    header("Location: index.php");
    exit();
}

else 
if (isset($_POST['action']) && $_POST['action'] == 'login'){

    //Require database connection
    require "files/dbconfig.php";
    //Creating connection object
    $con = new DBConfig();

    //Require class authentication
    require 'files/authentication.php';
    $auth = new authentication;

    //Require functions file
    require 'includes/functions.php';

    //Initialising variables
    $emailid = validData($_POST['username']);
    $password = $_POST['password'];

    //Exit the script if entities are emppty
    if (empty($emailid) || empty($password)){
        echo "Empty fields";
        exit();
    }

    else {
        $sql = "SELECT * FROM users WHERE uname = ? OR uemail = ? LIMIT 1; ";
        
        $paramType = "ss";

        $stmt = $con->conn->prepare($sql);
        $stmt->bind_param($paramType, $emailid, $emailid);

        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows > 0){

            //Fetching results
            $row = $result->fetch_assoc();         

            //Checking password
            $pwdHash = password_verify($password, $row['upwd']);
            if ($pwdHash == 0){

                //Send a message to login page that password is wrong
                echo 0;
                exit();
            }
            else if ($pwdHash == 1){
                $_SESSION['loggedIn'] = true;
                $_SESSION['uid'] = $row['uid'];

                $auth->lastActive($row['uid']);
                //Send a message to login page that login is successful
                echo 1;

                exit();
            }
            else {

                //Send a message saying some error occured
                echo -1;
            }
        }
        else {
            //User not found 
            echo 2;
        }
    }
}

else
if (isset($_POST['action']) && $_POST['action'] == 'register'){

    //Require database connection
    require "files/dbconfig.php";
    //Creating connection object
    $con = new DBConfig();

    //Require class authentication
    require 'files/authentication.php';
    $auth = new authentication;

    //Require functions file
    require 'includes/functions.php';

    //Require notification class
    require 'files/notificationClass.php';
    $notification = new notification();

    $fname = ucwords(validData($_POST['fname']));
    $lname = ucwords(validData($_POST['lname']));
    $uname = validData($_POST['uname']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $passwordRepeat = $_POST['cpassword'];

    if (empty($uname) || empty($fname) || empty($email) || empty($password) || empty($passwordRepeat)){
        echo 'empty';
        exit();
    }

    else
    if (!preg_match("/^[a-zA-Z]*$/", $fname)){
        echo 'fname';
        exit();
    }

    else
    if (!preg_match("/^[a-zA-Z]*$/", $lname)){
        echo 'lname';
        exit();
    }

    else
    if (!preg_match("/^[a-zA-Z0-9_]*$/", $uname)){
        echo 'uname';
        exit();
    }

    else
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
        echo 'email';
        exit();
    }

    else
    if ($password !== $passwordRepeat){
        echo 'mismatch';
        exit();
    }

    $unameexists = $auth->checkUsername($uname);
    if ($unameexists == -1){
        echo 'name exists';
        exit();
    }

    $mailexists = $auth->checkMail($email);
    if ($mailexists == -1){
        echo 'mail exists';
        exit();
    }

    else {

        date_default_timezone_set("Asia/Calcutta");
        $dor = date("Y-m-d H:i:s");

        $hashedPwd = password_hash($password, PASSWORD_DEFAULT);
        $userLevel = 1;
        $image =  'profile.png';
        
        $query = 'INSERT INTO users (ulvl, uname, uemail, upwd) VALUES (?, ?, ?, ?);';
        $query2 = 'INSERT INTO userdetails (image, firstName, lastName, dor, lastActiveTime) VALUES (?, ?, ?, ?, ?);';

        $stmt = $con->conn->prepare($query);
        $stmt2 = $con->conn->prepare($query2);

        $stmt->bind_param('isss', $userLevel, $uname, $email, $hashedPwd);
        $stmt2->bind_param('sssss', $image, $fname, $lname, $dor, $dor);

        $stmt->execute();
        $stmt2->execute();

        $lastid = $con->conn->insert_id;

        $message = 'Welcome '.$fname.'!! Thanks for joining us .' ;
        $notification->insertNoti($lastid, $message);

        $_SESSION['loggedIn'] = true;
        $_SESSION['uid'] = $lastid;

        echo 1;

    }
}
else {
    header("Location: /login.php");
}

?>