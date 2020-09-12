<?php
session_start();
//Checking for session
$loggedIn = isset($_SESSION['loggedIn']) ? $_SESSION['loggedIn'] : false;
if ($loggedIn == true){
    header("Location: index.php");
    exit();
}

$history = isset($_GET['signup']) ? $_GET['signup'] : false;

if ($history == 'new'){
    $history = -2;
} else {
    $history = -1;
}

//Require database connection
require "files/dbconfig.php";
$con = new DBConfig();

//Including categories files
include "./files/categories.php";
//Creating object for category
$categoryObj = new Categories();

//Include meta information
include "files/meta.php";

?>
<!DOCTYPE html>
<html lang="en" data-theme='light'>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#333">

    <meta name="keywords" content="Signup, <?php echo $metaInfo['keywords'];?>">
    <meta name="description" content="<?php echo $metaInfo['description']?> ">

    <meta property="og:title" content="Signup - <?php echo $metaInfo['keywords']?> " />
    <meta property="og:description" content="<?php echo $metaInfo['ogDescription']?> " />
    <meta property="og:image" content="<?php echo $metaInfo['domain'].'/'.$metaInfo['ogImage']?> " />
    <meta property="og:url" content="<?php echo $metaInfo['ogUrl']?> " />

    <link rel="stylesheet" href="<?php echo $metaInfo['domain'];?>/css/styles.css">
    <link rel="stylesheet" href="<?php echo $metaInfo['domain'];?>/css/dark.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="icon" href="<?php echo $metaInfo['domain'];?>/images/logo.png" type="image/png">
    <style>
        .xyw{
            height: 775px;
            transform: translateY(-20px);
        }
        @media only screen and (max-width: 600px){
            .xyw{
                height: 700px;
                transform: translateY(-10px);
            }
        }
    </style>
    <title>Signup - <?php echo $metaInfo['keywords'];?></title>
</head>
<body>
    <header>
        <?php
        include "includes/header.php";
        ?>
    </header>

    <main>
        <div id="doc">
            <div class="xyw">
                <div class="wrapper">
                    <h2>Register</h2>
                    <div class="f-avtar">
                        <img src="images/avtar.png" alt="">
                    </div>
                    <form id="signup-form" action="<?php echo $metaInfo['domain']; ?>/registeration.php" method="POST">
                        <div class="f-inp">
                            <input type="text" id="fname" name="fname" required />
                            <label for="uname">
                                <i class="fa fa-user" aria-hidden="true"></i>
                                <span>First Name</span>
                            </label>
                        </div>
                        <div class="f-inp">
                            <input type="text" id="lname" name="lname" required />
                            <label for="uname">
                                <i class="fa fa-user" aria-hidden="true"></i>
                                <span>Last Name</span>
                            </label>
                        </div>
                        <div class="f-inp">
                            <input type="text" id="uname" name="uname" required />
                            <label for="uname">
                                <i class="fa fa-user" aria-hidden="true"></i>
                                <span>Username</span>
                            </label>
                        </div>
                        <div class="f-inp">
                            <input type="email" id="email" name="email" required />
                            <label for="email">
                                <i class="fa fa-envelope" aria-hidden="true"></i>
                                <span>Email</span>
                            </label>
                        </div>
                        <div class="f-inp">
                            <input type="password" id="password" name="password" required />
                            <label for="password">
                                <i class="fa fa-lock" aria-hidden="true"></i>
                                <span>Password</span>
                            </label>
                            <i class="fa fa-eye showpwd" aria-hidden="true"></i>
                        </div>
                        <div class="f-inp">
                            <input type="password" id="cpassword" name="cpassword" required />
                            <label for="cpassword">
                                <i class="fa fa-lock" aria-hidden="true"></i>
                                <span>Re enter password</span>
                            </label>
                            <i class="fa fa-eye showpwd" aria-hidden="true"></i>
                        </div>
                        <div class="f-l-e" style="text-align: left; transform: translateY(-12px);">
                            
                        </div>
                        <div class="f-btn">
                            <button type="submit" name="signup-btn" class="f-btns">Register</button>
                        </div>
                    </form>
                    <div class="f-an">
                        <a href="login.php">Already have an account?</a>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <footer>
        <?php 
        include 'includes/footer.php'
        ?>
    </footer>
</body>
<script
    src="https://code.jquery.com/jquery-3.5.1.js"
    integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc="
    crossorigin="anonymous">
</script>

    <?php

    //Include dark mode script
    include "includes/dark_mode_script.php";
    //Include navigation bar script
    include "includes/nav_toggle_js.php";

    ?>
    <script>
         function toggleVisible(){
            var x = document.getElementById('password');
            if (x.type === 'password'){
                x.type = 'text';
            } else {
                x.type = 'password';
            }
        }

        $(document).ready(function(){

            $('.showpwd').click(function(){
                $('.showpwd').toggleClass('shown');
                toggleVisible();
            });

            var form = $('#signup-form');

            form.submit(function(e){
                e.preventDefault();

                var fname = $('#fname').val().trim();
                var lname = $('#lname').val().trim();
                var uname = $('#uname').val().trim();
                var email = $('#email').val().trim();
                var password = $('#password').val();
                var cpassword = $('#cpassword').val();

                const action = 'register';

                $.ajax({
                    url: form.attr('action'),
                    type: form.attr('method'),
                    data: {
                        fname: fname,
                        lname: lname,
                        uname: uname,
                        email: email,
                        password: password,
                        cpassword: cpassword,
                        action: action
                    },

                    success: function(response){

                        var msg = '';
                        if (response == 'empty'){
                            msg = 'Empty fields';
                        } else if (response == 'fname'){
                            msg = 'Invalid first name';
                        } else if (response == 'lname'){
                            msg = 'Invalid last name';
                        } else if (response == 'uname'){
                            msg = 'Invalid uname name';
                        } else if (response == 'email'){
                            msg = 'Invalid email address';
                        } else if (response == 'mismatch'){
                            msg = 'Passwords do not match';
                        } else if (response == 'name exists'){
                            msg = 'Username already exists. Please try a different one';
                        } else if (response == 'mail exists'){
                            msg = '<span style="color: green">There is already an account with this email address.</span>';
                        }
                        if (response == 1){
                            msg = '<span style="color: green;">Signup successful. Redirecting to previous page.</span>';
                            setTimeout(function(){
                                window.history.go(<?php echo $history; ?>);
                            }, 1000);
                        }

                        $('.f-l-e').html(msg);
                    }
                })
            })
        })
    </script>

</html>