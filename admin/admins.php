<?php

//Require database connection
require "../files/dbconfig.php";
$con = new DBConfig();

//Require class user to get user details
require "../files/user.php";
$user = new User();

//Require notification class
require '../files/notificationClass.php';
$notification = new notification();

//Including categories files
include "../files/categories.php";
$categoryObj = new Categories();

//Require admin class
require 'adminclass.php';
$admin = new admin();

//Require meta info for creating links
require '../files/meta.php';

session_start();

//Checking for session
$loggedIn = isset($_SESSION['loggedIn']) ? $_SESSION['loggedIn'] : false;

if ($loggedIn == false){
    echo '<html>
            <head>
                <title>404 Not Found</title>
                <style>
                    *{
                        margin: 0;
                        padding: 0;
                    }
                </style>                
            <link rel="icon" href="'.$metaInfo ['domain'].'/images/logo.png" type="image/png">
            </head>
            <body>';
    echo '<iframe src="'.$metaInfo['domain'].'/error.php" frameBorder="0" width="100%" height="100%"></iframe>'  ;
    echo '</body></html>';
    exit();
}

$sessionUserLevel = $user->userLevel($_SESSION['uid']);
if ($sessionUserLevel != 4 && $sessionUserLevel != 5){
    echo '<html>
            <head>
                <title>404 Not Found</title>
                <style>
                    *{
                        margin: 0;
                        padding: 0;
                    }
                </style>                
            <link rel="icon" href="'.$metaInfo ['domain'].'/images/logo.png" type="image/png">
            </head>
            <body>';
    echo '<iframe src="'.$metaInfo['domain'].'/error.php" frameBorder="0" width="100%" height="100%"></iframe>'  ;
    echo '</body></html>';
    exit();
}

?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#333">

    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/dark.css">
    <link rel="stylesheet" href="css/stylesheet.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="icon" href="../images/logo.png" type="image/png">

    <title>View Admins</title>
</head>
<body>

<header>
<?php
include '../includes/header.php';
?>
</header>

<main>
    <div id="wrapper">
        <div class="a-options">               
        <ul class="aUL">
            <a href="iddex.php">
                <li>
                    <i class="fa fa-user" aria-hidden="true"></i>
                    Dashboard
                </li>
            </a>
            <a href="feature-posts.php">
                <li>
                    <i class="fa fa-user" aria-hidden="true"></i>
                    Feature Posts
                </li>
            </a>
            <a href="admins.php">
                <li class="active">
                    <i class="fa fa-user" aria-hidden="true"></i>
                    View admins
                </li>
            </a>
            <a href="change_ug.php">
                <li>
                    <i class="fa fa-user" aria-hidden="true"></i>
                    Change User groups
                </li>
            </a>  
            <a href="ban_user.php"> 
                <li>
                    <i class="fa fa-user" aria-hidden="true"></i>
                    Ban Users
                </li> 
            </a>
        </ul>
        </div>
        <div class="a-content">
            <div class="atitle">
                <h2 style="text-align: center; color: green;">Current Admins</h2>
            </div>
            <div class="va">
                <table>
                    <thead>
                        <tr>
                            <td>Avtar</td>
                            <td>User name</td>
                            <td>Email</td>
                            <?php
                            if ($sessionUserLevel == 5){
                                echo '
                                <td>Remove admin</td>
                                ';
                            }
                            ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $viewAdmins = $admin->viewAdmins();
                        if ($viewAdmins != 0){
                            foreach ($viewAdmins as $rowAdmins){

                                $profileImage = $user->profileImage($rowAdmins['uid']);
                                echo '
                                <tr>
                                <td><img src="../profileImages/'.$profileImage.'" alt=""></td>
                                <td><a href="../view-profile.php?uid='.$rowAdmins['uid'].'">'.$rowAdmins['uname'].'</a></td>
                                <td><a href="mailto:'.$rowAdmins['uemail'].'">'.$rowAdmins['uemail'].'</a></td>
                                ';
                                if ($sessionUserLevel == 5){
                                    echo '
                                        <td><a href="change_ug.php?uid='.$rowAdmins['uid'].'">Remove</a></td>
                                    ';
                                }
                                echo '
                                </tr>
                                ';
                            }
                        }
                        else{
                            echo 'No admins currently';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

</body>
<script
    src="https://code.jquery.com/jquery-3.5.0.js"
    integrity="sha256-r/AaFHrszJtwpe+tHyNi/XCfMxYpbsRg2Uqn0x3s2zc="
    crossorigin="anonymous"></script>
<?php

    //Include dark mode script
    include "../includes/dark_mode_script.php";
    //Include navigation bar script
    include "../includes/nav_toggle_js.php";

    //Include notifications
    include '../load-notifications.php';
    
    ?>

</html>



