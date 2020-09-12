<?php
session_start();

//Checking for session
$loggedIn = isset($_SESSION['loggedIn']) ? $_SESSION['loggedIn'] : false;

//Require database connection
require "files/dbconfig.php";
$con = new DBConfig();

//Require class user to get user details
require "files/user.php";
$user = new User();

//Including categories files
include "files/categories.php";
$categoryObj = new Categories();

//Require the functions page
require "includes/functions.php";

//Include meta information
include "files/meta.php";

?>
<!DOCTYPE html>
<html lang="en" data-theme='light'>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#333">
    <link rel="stylesheet" href="<?php echo $metaInfo['domain']; ?>/css/styles.css">
    <link rel="stylesheet" href="<?php echo $metaInfo['domain']; ?>/css/dark.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="icon" href="<?php echo $metaInfo['domain']; ?>/images/logo.png" type="image/png">

    <script
    src="https://code.jquery.com/jquery-3.5.0.js"
    integrity="sha256-r/AaFHrszJtwpe+tHyNi/XCfMxYpbsRg2Uqn0x3s2zc="
    crossorigin="anonymous"></script>

    <title>Forgot Password - <?php echo $metaInfo['keywords'];?></title>
</head>
<body>
    <header>
        <?php
        include 'includes/header.php';
        ?>
    </header>

    <main>
        <div id="doc">      
            <div class="xyw">
                <div class="wrapper">
                    <p>Forgot your password?</p>
                    <p>Enter your email or username to get password reset link on your registered email</p>
                    <div class="f-inp">
                        <input type="text" id="name" name="umailid" required />
                        <label for="umailid">
                            <i class="fa fa-user" aria-hidden="true"></i>
                            <span>Username/email address</span>
                        </label>
                    </div>
                    <div class="f-l-e" style="margin-bottom: 10px;">

                    </div>
                    <div class="f-btn">
                        <button type="button" name="login" class="f-btns" id="login" style="width: 60%; letter-spacing: 1px;">Request Link</button>
                    </div>
                    <div class="f-an">
                        Back to <a href="<?php echo $metaInfo['domain']; ?>/login.php" style="text-decoration: underline;">login page</a>
                    </div>
                </div>
            </div>
        </div>  
    </main>
    
    <footer>
    <?php
    include 'includes/footer.php';
    ?>
    </footer>
    <?php

    //Include dark mode script
    include "includes/dark_mode_script.php";
    //Include navigation bar script
    include "includes/nav_toggle_js.php";

    //Include notifications
    include 'load-notifications.php';
    
    ?>

    <script>
        $(document).ready(function(){
            $('.f-btns').click(function(){

                var name = $('#name').val().trim();
                const action = 'rp';

                $.ajax({
                    url: '<?php echo $metaInfo['domain']; ?>/forgotpassword.php',
                    type: 'post',
                    data: {
                        name: name,
                        action: action
                    },
                    success: function(data){
                        var msg = '';

                        if (data == 0){
                            msg = 'User not found';
                        }
                        if (data == 1){
                            msg = '<span style="color: green;">A mail has been sent to your email id with a link to reset your password. ';
                            msg += 'Make sure you haven\'t kept us in spam.</span>';
                        }

                        $('.f-l-e').html(msg);
                    }
                });
            });
        });
    </script>
</body>
</html>