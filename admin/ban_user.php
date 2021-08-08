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

$uid = isset($_GET['uid']) ? $_GET['uid'] : '';
?>
<!DOCTYPE html>
<html lang="en" theme='light'>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="theme-color" content="#333">

    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/dark.css">
    <link rel="stylesheet" href="css/stylesheet.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="icon" href="../images/logo.png" type="image/png">

    <script
    src="https://code.jquery.com/jquery-3.5.0.js"
    integrity="sha256-r/AaFHrszJtwpe+tHyNi/XCfMxYpbsRg2Uqn0x3s2zc="
    crossorigin="anonymous"></script>

    <title>Ban User</title>
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
            <a href="index.php">
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
                <li>
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
                <li class="active">
                    <i class="fa fa-user" aria-hidden="true"></i>
                    Ban Users
                </li> 
            </a>
        </ul>
        </div>
        <div class="a-content">
            <div class="atitle">
                <h2>Ban User</h2>
            </div>
            <div class="form-wrap" style="padding: 40px;">
                <form action="adminAction.php" id="ban" method="POST" autocomplete="off">
                    <div class="ipanel">
                        <input type="number" name="uidb" id="uidb" class="ipinp" placeholder="Enter uid to ban" value="<?php echo $uid; ?>" />
                        <textarea name="" id="" placeholder="Enter reason for banning (Optional)"></textarea>
                    </div>
                    <div class="fBtn" style="margin-top: 20px;">
                        <button type="submit" name="fbA" class="f-btn">Ban</button>
                    </div>
                </form>
            <div class="a-msg" style="color: red;">

            </div>
            </div>
        </div>
    </div>
</main>
    <script>
    function validate(){
        var uid = document.getElementById("uidb").value;

        if (uid == ''){
            alert("Please enter uid to ban");
            return false;
        }

        else 
        if (isNaN(uid)){
            alert("Please enter a valid uid");
            return false;
        }

        return true;
    }

    $(document).ready(function(){

        var form = $("#ban");
        
        form.submit(function(e){
            e.preventDefault();

            var valid = validate();

            var uid = $("#uidb").val().trim();
            const action = 'ban';

            if( valid == true){
                $.ajax({
                    url: form.attr("action"),
                    type: form.attr("method"),
                    data: {
                        uid: uid,
                        action: action
                    },

                    success: function(response){

                        var msg = "";
                        if(response == 1){
                            msg = '<span style="color: green;">Banned successfully. </span>';                            
                        } 
                        else if (response == 'admin'){
                            msg = "Cannot ban a user with admin rights!";
                        } 
                        else if (response == 0){
                            msg = 'User not found';
                        }
                        else {
                            msg = "Some error occured. Refresh the page and try again."
                        }
                        $(".a-msg").html(msg);

                    }

                });
            }
        });
    });
    </script>
    <?php

//Include dark mode script
include "../includes/dark_mode_script.php";
//Include navigation bar script
include "../includes/nav_toggle_js.php";

//Include notifications
include '../load-notifications.php';

?>
</body>
</html>