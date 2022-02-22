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

//Including categories files
include "files/categories.php";
//Creating object for category
$categoryObj = new Categories();

//Require meta file
require 'files/meta.php';

//Message variable to show the message to the user
$msg = '';
$found = 0;
$greetUserName = '';

$selector = isset($_GET['selector']) ? $_GET['selector'] : false;
$validator = isset($_GET['validator']) ? $_GET['validator'] : false;

if ($selector == false || $validator == false){
    $msg = '<div style="color: var(--textM);">';
    $msg .= 'Something went wrong. If you think you are here by mistake. ';
    $msg .= 'Click <a href="'.$metaInfo['domain'].'" style="color: var(--gradientE);">here</a> to go to homepage. ';
    $msg .= '</div>';
} else {
    if (ctype_xdigit($selector) !== false && ctype_xdigit($validator) !== false){
        
        $resetData = $auth->pwdResetData($selector);
        if ($resetData != -1){
            $uid = $resetData['uid'];
            $found = 1;
            $greetUserName = $user->userName($uid);
        }

        else{
            $msg = '<div style="color: var(--textM);" >';
            $msg .= 'The link seems to be broken. You need to request for resetting password again. ';
            $msg .= 'Click <a href="'.$metaInfo['domain'].'/forgot-password.php" style="color: var(--gradientE);">here </a>to request again. <br>';
            $msg .= 'Click <a href="'.$metaInfo['domain'].'" style="color: var(--gradientE);">here</a> to go to homepage. ';
            $msg .= '</div>';
        }

    } else{
        $msg = '<div style="color: var(--textM);" >';
        $msg .= 'The link seems to be broken. You need to request for resetting password again. ';
        $msg .= 'Click <a href="'.$metaInfo['domain'].'/forgot-password.php" style="color: var(--gradientE);">here </a>to request again. <br>';
        $msg .= 'Click <a href="'.$metaInfo['domain'].'" style="color: var(--gradientE);">here</a> to go to homepage. ';
        $msg .= '</div>';
    }
}
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

    <title>Reset Password - <?php echo $metaInfo['keywords'];?></title>
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
                    <?php
                    if ($found == 1){
                    ?>
                    <p>Reset your password</p>
                    <p>Hello <?php echo $greetUserName;?>, <br>Please enter your new password. Make sure to use a strong password of minimum 6 charachters.</p>
                    <form id="reset-form" action="resetpassword.php" method="post">
                        <input type="hidden" id="selector" name="selector" value="<?php echo $selector;?>">
                        <input type="hidden" id="validator" name="validator" value="<?php echo $validator; ?>">
                        <div class="f-inp">
                            <input type="password" id="password" name="password" required />
                            <label for="password">
                                <i class="fa fa-lock" aria-hidden="true"></i>
                                <span>Enter new password</span>
                            </label>
                            <i class="fa fa-eye showpwd" aria-hidden="true"></i>
                        </div>
                        <div class="f-inp">
                            <input type="password" id="cpassword" name="cpassword" required />
                            <label for="cpassword">
                                <i class="fa fa-lock" aria-hidden="true"></i>
                                <span>Re-enter new password</span>
                            </label>
                            <i class="fa fa-eye showpwd" aria-hidden="true"></i>
                        </div>
                        <div class="f-l-e" style="margin-bottom: 10px;">

                        </div>
                        <div class="f-btn">
                            <button type="submit" class="f-btns" id="login" style="width: 60%; letter-spacing: 1px;">Reset Password</button>
                        </div>
                    </form>
                    <?php
                    } else {
                        echo $msg;
                    }
                    ?>
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

        var form = $("#reset-form");
        
        form.submit(function(e){
            e.preventDefault();

            var password = $("#password").val().trim();
            var cpassword = $("#cpassword").val().trim();
            const selector = $('#selector').val().trim();
            const validator = $('#validator').val().trim();
            const action = 'rp';

            if( password != "" && cpassword != "" ){
                $.ajax({
                    url: form.attr("action"),
                    type: form.attr("method"),
                    data: {
                        password: password,
                        cpassword: cpassword,
                        selector: selector,
                        validator: validator,
                        action: action
                    },

                    success: function(response){
                        var msg = "";
                        if(response == 1){
                            msg = '<span style="color: green;">Password reset successful. Redirecting you to login page</span>';
                            setTimeout(function(){
                                window.location.assign('<?php echo $metaInfo['domain']; ?>/login.php');
                            }, 1000);
                        } 
                        else if (response == 0){
                            msg = "The link seems to be broken!";
                        } else if (response == 'mismatch'){
                            msg = "Passwords do not match.";
                        }
                        else if (response == 2){
                            msg = "*The link seems to be broken or expired. Please check the link or request for password reset again.";
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