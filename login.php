<?php
session_start();
//Checking for session
$loggedIn = isset($_SESSION['loggedIn']) ? $_SESSION['loggedIn'] : false;
if ($loggedIn == true){
    header("Location: index.php");
    exit();
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

    <meta name="keywords" content="login, <?php echo $metaInfo['keywords']; ?>">
    <meta name="description" content="<?php echo $metaInfo['description']?> ">

    <meta property="og:title" content="Login - <?php echo $metaInfo['keywords']?> " />
    <meta property="og:description" content="<?php echo $metaInfo['ogDescription']?> " />
    <meta property="og:image" content="<?php echo $metaInfo['ogImage']?> " />
    <meta property="og:url" content="<?php echo $metaInfo['ogUrl']?> " />

    <link rel="stylesheet" href="<?php echo $metaInfo['domain']; ?>/css/styles.css">
    <link rel="stylesheet" href="<?php echo $metaInfo['domain']; ?>/css/dark.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="icon" href="<?php echo $metaInfo['domain']; ?>/images/logo.png" type="image/png">

    <script
    src="https://code.jquery.com/jquery-3.5.1.js"
    integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc="
    crossorigin="anonymous">
    </script>
    
    <title>Login - <?php echo $metaInfo['keywords']; ?></title>
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
                        <h2>Login</h2>
                        <div class="f-avtar">
                            <img src="<?php echo $metaInfo['domain']; ?>/images/avtar.png" alt="">
                        </div>
                        <form action="<?php echo $metaInfo['domain']; ?>/registeration.php" method="POST" id="login-form">
                            <div class="f-inp">
                                <input type="text" id="name" name="umailid" required />
                                <label for="name">
                                    <i class="fa fa-user" aria-hidden="true"></i>
                                    <span>Username</span>
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
                            <div class="f-l-e" style="margin-bottom: 10px;">

                            </div>
                            <div class="f-btn">
                                <button type="submit" name="login" class="f-btns" id="login" value="login">Login</button>
                            </div>
                        </form>
                        <div class="f-an">
                            <a href="<?php echo $metaInfo['domain']; ?>/signup.php?signup=new">Don't have an account?</a>
                        </div>
                        <div class="f-an" style="margin-top: 8px;">
                            <a href="<?php echo $metaInfo['domain']; ?>/forgot-password.php">Forgot your password?</a>
                        </div>
                    </div>
                </div>
            </div>
    </main>

    <footer>
        <div class="footer">
            Copyright &copy; 2020 Mayank
        </div>
    </footer>

    <?php

    //Include dark mode script
    include "includes/dark_mode_script.php";
    //Include navigation bar script
    include "includes/nav_toggle_js.php";
    
    ?>
    <script type="text/javascript">

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

        var form = $("#login-form");
        
        form.submit(function(e){
            e.preventDefault();


            var username = $("#name").val().trim();
            var password = $("#password").val().trim();
            const action = 'login';

            if( username != "" && password != "" ){
                $.ajax({
                    url: form.attr("action"),
                    type: form.attr("method"),
                    data: {
                        username: username,
                        password: password,
                        action: action
                    },

                    success: function(response){

                        var msg = "";
                        if(response == 1){
                            msg = '<span style="color: green;">Login successful. Redirecting you to previous page</span>';
                            setTimeout(function(){
                                window.history.back();
                            }, 1000);
                        } 
                        else if (response == 0){
                            msg = "Invalid password!";
                        } 
                        else if (response == 2){
                            msg = "*User not found";
                        }
                        else {
                            msg = "Some error occured. Refresh the page and try again."
                        }
                        $(".f-l-e").html(msg);

                    }

                });
            }
        });
    });

    </script>
</body>
</html>